<?php

namespace App\Service;

use App\Entity\PlanHistory;
use App\Entity\Subscription;
use App\Factory\InvoiceFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Twig\Environment;

/**
 * InvoiceService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class InvoiceService
{
    private $em;
    private $mailer;
    private $templating;

    public function __construct(
        Environment $templating,
        Swift_Mailer $mailer,
        EntityManagerInterface $em
    ) {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function createInvoice(Subscription $subscription, PlanHistory $planHistory)
    {
        $this->em->beginTransaction();

        try {
            $invoice = InvoiceFactory::createInvoice($subscription, $planHistory);

            $this->em->persist($invoice);

            $this->em->commit();

            $this->em->flush();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    public function proccessInvoice()
    {
    }
}
