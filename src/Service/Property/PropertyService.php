<?php

namespace App\Service\Property;

use App\Entity\Resource\Property;
use App\Entity\Resource\Resource;
use App\Enum\PropertyStatus;
use App\Service\ResourceService;
use Exception;

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
}
