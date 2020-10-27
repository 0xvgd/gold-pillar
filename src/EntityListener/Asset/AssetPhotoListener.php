<?php

namespace App\EntityListener\Asset;

use App\Entity\Asset\AssetPhoto as Entity;
use App\Utils\FileUploadManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * AssetPhotoListener.
 *
 * @author Laerte Mercier <laertejjunior@gmail.com>
 */
class AssetPhotoListener
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

    public function deletePhoto(Entity $photo)
    {
        $this->removeFile($photo);
    }

    public function removeFile(Entity $photo)
    {
        $url = $photo->getPath();
        if ($url) {
            $this->uploader->delete($url);
        }
    }
}
