<?php

namespace App\EntityListener;

use App\Entity\Sales\PropertyPhoto as Entity;
use App\Utils\FileUploadManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * PropertyPhotoListener.
 *
 * @author Laerte Mercier <laertejjunior@gmail.com>
 */
class PropertyPhotoListener
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
