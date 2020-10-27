<?php

namespace App\EntityListener\Renting;

use App\Entity\Resource\Accommodation;
use App\Service\Renting\RentingService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class AccommodationListener
{
    private $service;

    public function __construct(RentingService $service)
    {
        $this->service = $service;
    }

    public function prePersist(Accommodation $accommodation, LifecycleEventArgs $args): void
    {
    }

    public function preUpdate(Accommodation $accommodation, PreUpdateEventArgs $args)
    {
    }
}
