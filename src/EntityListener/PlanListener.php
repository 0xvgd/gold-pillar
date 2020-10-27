<?php

namespace App\EntityListener;

use App\Entity\Plan;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PlanListener
{
    public function prePersist(Plan $user, LifecycleEventArgs $args): void
    {
        $user->setCreatedAt(new DateTime());
    }
}
