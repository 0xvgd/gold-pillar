<?php

namespace App\EntityListener\Finance;

use App\Entity\Finance\AssetInvestmentTransaction as Entity;
use DateTime;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class AssetInvestmentTransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }
}
