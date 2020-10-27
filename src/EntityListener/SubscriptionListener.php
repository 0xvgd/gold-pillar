<?php

namespace App\EntityListener;

use App\Entity\PlanHistory;
use App\Entity\Subscription as Entity;
use App\Service\InvoiceService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SubscriptionListener
{
    /** @var EntityManagerInterface */
    private $em;
    private $params;
    private $invoiceService;

    public function __construct(
        EntityManagerInterface $em,
        InvoiceService $invoiceService,
        ParameterBagInterface $params
    ) {
        $this->em = $em;
        $this->params = $params;
        $this->invoiceService = $invoiceService;
    }

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
