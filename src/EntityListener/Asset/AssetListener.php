<?php

namespace App\EntityListener\Asset;

use App\Entity\Resource\Asset;
use App\Service\Asset\AssetService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class AssetListener
{
    private $service;

    public function __construct(AssetService $service)
    {
        $this->service = $service;
    }

    public function prePersist(Asset $project, LifecycleEventArgs $args): void
    {
    }

    public function preUpdate(Asset $project, PreUpdateEventArgs $args)
    {
    }
}
