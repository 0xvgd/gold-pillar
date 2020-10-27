<?php

namespace App\EntityListener\Finance;

use App\Entity\Finance\ProjectInvestmentTransaction as Entity;
use DateTime;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class ProjectInvestmentTransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }
}
