<?php

namespace App\Service\Renting;

use App\Entity\Resource\Accommodation;
use App\Entity\Resource\Resource;
use App\Enum\AccommodationStatus;
use App\Service\ResourceService;
use Exception;

/**
 * RentingService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class RentingService extends ResourceService
{
    /**
     * @param Accommodation $accommodation
     *
     * @throws Exception
     */
    public function save(Resource $accommodation)
    {
        if (!$accommodation instanceof Accommodation) {
            throw new Exception('The resource must be a instance of Accommodation');
        }

        if (!$accommodation->getId()) {
            $accommodation->setReferenceCode($this->generateReferenceCode());
            $accommodation->setStatus(AccommodationStatus::TO_RENT());
        }

        if (!$accommodation->getReferenceCode()) {
            $accommodation->setReferenceCode($this->generateReferenceCode());
        }

        parent::save($accommodation);
    }

    /**
     * @param string $slug
     * @param int    $id
     */
    public function getTransactions(Accommodation $accommodation)
    {
        return [];
    }

    public function generateReferenceCode()
    {
        $referenceCode = null;
        $codeExists = true;

        while ($codeExists) {
            $referenceCode = 'RENT-'.substr(strtoupper(sha1(random_bytes(4))), 0, 8);
            $reserve = $this->getEntityManager()->getRepository(Accommodation::class)->findOneBy(
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
