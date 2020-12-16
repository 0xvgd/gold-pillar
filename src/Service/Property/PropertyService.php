<?php

namespace App\Service\Property;

use App\Entity\Resource\Property;
use App\Entity\Resource\Resource;
use App\Enum\PropertyStatus;
use App\Service\ResourceService;
use Exception;
use Symfony\Component\HttpClient\HttpClient;

/**
 * PropertyService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class PropertyService extends ResourceService
{
    /**
     * @param Property $property
     *
     * @throws Exception
     */
    public function save(Resource $property)
    {
        if (!$property instanceof Property) {
            throw new Exception('The resource must be a instance of Property');
        }

        if (!$property->getId()) {
            $property->setReferenceCode($this->generateReferenceCode());
            $property->setPropertyStatus(PropertyStatus::FOR_SALE());
        }

        if (!$property->getReferenceCode()) {
            $property->setReferenceCode($this->generateReferenceCode());
        }

        parent::save($property);
    }

    /**
     * @param string $slug
     * @param int    $id
     */
    public function getTransactions(Property $property)
    {
        return [];
    }

    public function generateReferenceCode()
    {
        $referenceCode = null;
        $codeExists = true;

        while ($codeExists) {
            $referenceCode = 'PROP-'.substr(strtoupper(sha1(random_bytes(4))), 0, 8);
            $reserve = $this->getEntityManager()->getRepository(Property::class)->findOneBy(
                [
                    'referenceCode' => $referenceCode,
                ]
            );

            if (!$reserve) {
                $codeExists = false;
            }
        }

        return $referenceCode;
    }

    public function getAddressByPostCode($pcode){
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', 'https://api.ideal-postcodes.co.uk/v1/addresses?api_key=ak_kiioxrwcsKlvY1AoF5EV328WnoBW8&query='.$pcode);
            $content = $response->toArray();
            $result = $content['result']['hits'];
            $items = [];
            foreach ($result as $one){
                $value = $one['line_1'];
                if($one['line_2'] != "")
                    $value .=" - ".$one['line_2'];
                if($one['line_3'] != "")
                    $value .=" - ".$one['line_3'];
                $addr = $value;
                $value .= " - ".$one['post_town'].' - '.$one['postcode'];
                $item = ['text' =>$value,'addr' => $addr,'city' =>$one['post_town'],'county' => $one['county']];
                $items[] = $item;
            }
            $items = array_chunk($items,5);
           /* $len = count($items);
            if(count($items[$len-1]) < 5 ){
                $len_item = count($items[$len-1]);
                for($i = 0;$i<5-$len_item;$i++){
                    $items[$len-1][] = null;
                }
            }*/
            return $items;
        } catch (Exception $e){
            return [];
        }
    }
}
