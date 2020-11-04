<?php

namespace App\EntityListener;

use App\Entity\PlanHistory;
use App\Entity\Subscription as Entity;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SubscriptionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
        $entity->setSubscribedAt(new DateTime());
    }

    protected function createPlanHistory(Entity $entity)
    {
        $planHistory = new PlanHistory();
        $planHistory->setSubscription($entity);
        $planHistory->setPlan($entity->getPlan());
        $planHistory->setStartDate($entity->getSubscribedAt());

        return $planHistory;
    }
}
