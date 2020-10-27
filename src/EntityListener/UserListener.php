<?php

namespace App\EntityListener;

use App\Entity\Security\User;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserListener
{
    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
        $user->setCreatedAt(new DateTime());
    }
}
