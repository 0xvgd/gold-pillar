<?php

namespace App\EntityListener\Finance;

use App\Entity\Finance\CompanyTransaction as Entity;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CompanyTransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }
}
