<?php

namespace App\EntityListener;

use App\Entity\ExecutiveClub as Entity;
use App\Utils\FileUploadManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * ExecutiveClubListener.
 *
 * @author Laerte Mercier <laertejjunior@gmail.com>
 */
class ExecutiveClubListener
{
    /**
     * @var FileUploadManager
     */
    private $uploader;

    public function __construct(FileUploadManager $uploader)
    {
        $this->uploader = $uploader;
    }

    public function preRemove(Entity $entity, LifecycleEventArgs $args)
    {
        $this->removeFile($entity);
    }

    public function removeFile(Entity $entity)
    {
        $url = $entity->getPathLogo();
        if ($url) {
            $this->uploader->delete($url);
        }
    }
}
