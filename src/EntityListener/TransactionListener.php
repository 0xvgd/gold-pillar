<?php

namespace App\EntityListener;

use App\Entity\Transaction as Entity;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }
}
