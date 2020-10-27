<?php

namespace App\EntityListener\Finance;

use App\Entity\Finance\ProjectTransaction as Entity;
use DateTime;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class ProjectTransactionListener
{
    public function prePersist(Entity $entity, LifecycleEventArgs $args): void
    {
        $entity->setCreatedAt(new DateTime());
    }

    // public function postPersist(LifecycleEventArgs $args)
    // {
    //     /** @var Entity */
    //     $entity = $args->getObject();

    //     // only act on some "Product" entity
    //     if (!$entity instanceof Entity) {
    //         return;
    //     }

    //     $transactionRef = $this->generateTransactionRef($entity);
    //     $entity->setTransactionRef($transactionRef);

    //     $entityManager = $args->getObjectManager();
    //     $entityManager->merge($entity);
    //     $entityManager->flush();
    //     // ... do something with the Product
    // }
}
