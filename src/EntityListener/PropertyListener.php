<?php

namespace App\EntityListener;

use App\Entity\Resource\Property;
use App\Service\Property\PropertyService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class PropertyListener
{
    private $service;

    public function __construct(PropertyService $service)
    {
        $this->service = $service;
    }

    public function prePersist(Property $property, LifecycleEventArgs $args): void
    {
    }

    public function preUpdate(Property $property, PreUpdateEventArgs $args)
    {
    }
}
