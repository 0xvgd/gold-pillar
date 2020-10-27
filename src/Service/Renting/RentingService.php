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
            $accommodation->setStatus(AccommodationStatus::TO_RENT());
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
}
