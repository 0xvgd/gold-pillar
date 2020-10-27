<?php

namespace App\EntityListener\Transactions;

use App\Entity\Transactions\PropertySaleTransaction as Entity;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PropertySaleTransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }
}
