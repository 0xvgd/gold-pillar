<?php

namespace App\EntityListener;

use App\Entity\PlanHistory;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PlanHistoryListener
{
    public function prePersist(PlanHistory $planHistory, LifecycleEventArgs $args): void
    {
        $planHistory->setCreatedAt(new DateTime());
    }
}
