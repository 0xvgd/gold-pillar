<?php

namespace App\Service;

use App\Entity\Negotiation\Offer;
use App\Entity\Person\Agent;
use App\Entity\Security\User;
use Exception;
use Swift_Message;

class OfferService
{
    /**
     * @param User $user
     *
     * @return int
     */
    public function sendOfferEmail(Agent $agent, Offer $offer)
    {
        $user = $agent->getUser();
        $email = $user->getEmail();
        $hash = User::generateHash($user);

        try {
            $message = (new Swift_Message('Gold Pillar | Renting offer'))
                ->setFrom('lmercierhomologa@gmail.com')
                ->setTo($email)
                ->setBody($this->getOfferEmailBody($email, $hash, $offer->getId()))
                ->setContentType('text/html');
            $this->mailer->send($message);
        } catch (Exception $ex) {
            throw new Exception("Error: {$ex->getMessage()}");
        }
    }

    /**
     * @param string $email
     * @param string $hash
     *
     * @return string
     */
    private function getOfferEmailBody($email, $hash)
    {
        $template = $this
            ->templating
            ->render('emails/offer/offer.html.twig', [
                'email' => $email,
                'hash' => $hash,
            ]);

        return $template;
    }
}
