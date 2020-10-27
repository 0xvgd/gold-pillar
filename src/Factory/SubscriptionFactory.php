<?php

namespace App\Factory;

use App\Entity\Plan;
use App\Entity\Security\User;
use App\Entity\Subscription;

/**
 * Description of SubscriptionFactory.
 *
 * @author Laerte Mercier laertejjunior@gmail.com
 */
class SubscriptionFactory
{
    public static function createSubscription(Plan $plan, User $user)
    {
        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setPlan($plan);

        return $subscription;
    }
}
