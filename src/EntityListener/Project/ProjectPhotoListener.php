<?php

namespace App\EntityListener\Project;

use App\Entity\Project\ProjectPhoto as Entity;
use App\Utils\FileUploadManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * ProjectPhotoListener.
 *
 * @author Laerte Mercier <laertejjunior@gmail.com>
 */
class ProjectPhotoListener
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
        $this->deletePhoto($entity);
    }

    public function deletePhoto($photo)
    {
        $this->removeFile($photo);
    }

    public function removeFile($photo)
    {
        $url = $photo->getPath();
        if ($url) {
            $this->uploader->delete($url);
        }
    }
}
