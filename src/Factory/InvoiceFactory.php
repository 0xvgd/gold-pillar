<?php

namespace App\Factory;

use App\Entity\PlanHistory;
use App\Entity\Subscription;

/**
 * Description of InvoiceFactory.
 *
 * @author Laerte Mercier laertejjunior@gmail.com
 */
class InvoiceFactory
{
    /**
     * @return Subscription
     */
    public static function createInvoice(Subscription $subscription, PlanHistory $planHistory)
    {
        $user = $subscription->getUser();
        $invoice = new \App\Entity\Invoice();

        $invoice->setUserData($user->getName());
        $invoice->setSubscription($subscription);
        $invoice->setDescription('Yearly invoice');
        $invoice->setPlanHistory($planHistory);
        $invoice->setAmount($planHistory->getPlan()->getPrice());
        $invoice->setDueDate(new \DateTime());

        return $invoice;
    }
}
