<?php

namespace App\EntityListener\Finance;

use App\Entity\Finance\UserTransaction as Entity;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserTransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }
}
