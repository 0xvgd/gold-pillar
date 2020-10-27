<?php

namespace App\EntityListener\Finance;

use App\Entity\Finance\PropertyTransaction as Entity;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PropertyTransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }
}
