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
            $property->setPropertyStatus(PropertyStatus::FOR_SALE());
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
}
