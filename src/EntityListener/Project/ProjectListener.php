<?php

namespace App\EntityListener\Project;

use App\Entity\Resource\Project;
use App\Service\Project\ProjectService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ProjectListener
{
    private $service;

    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function prePersist(Project $project, LifecycleEventArgs $args): void
    {
    }

    public function preUpdate(Project $project, PreUpdateEventArgs $args)
    {
    }
}
