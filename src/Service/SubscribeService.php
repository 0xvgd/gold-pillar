<?php

namespace App\Service;

use App\Entity\Person\Agent;
use App\Entity\Person\Broker;
use App\Entity\Person\Investor;
use App\Entity\Person\Person;
use App\Entity\Plan;
use App\Entity\Security\User;
use App\Factory\SubscriptionFactory;
use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use Twig\Environment;

/**
 * SubscribeService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class SubscribeService
{
    private $em;
    private $mailer;
    private $templating;
    private $userService;

    public function __construct(
        Environment $templating,
        Swift_Mailer $mailer,
        EntityManagerInterface $em,
        UserService $userService
    ) {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->em = $em;
        $this->userService = $userService;
    }

    /**
     * @throws Exception
     */
    public function registerToPlan(User $user, Plan $plan)
    {
        $this->em->beginTransaction();
        try {
            $planCode = strtoupper($plan->getCode());
            $roleName = "ROLE_$planCode";

            $person = null;
            $user->setRoles([$roleName]);

            switch ($plan->getCode()) {
                case Plan::PLAN_AGENT:
                    $person = new Agent();
                    $person->setDescription('');
                    break;
                case Plan::PLAN_BROKER:
                    $person = new Broker();
                    $person->setDescription('');
                    break;
                case Plan::PLAN_INVESTOR:
                    $person = new Investor();
                    $person->setDescription('');
                    break;
                default:
                    break;
            }

            if ($person instanceof Person) {
                $person->setUser($user);
                $this->em->persist($person);
            }

            $subscription = SubscriptionFactory::createSubscription($plan, $user);

            $this->em->persist($subscription);
            $this->em->flush();
            $this->em->commit();

            $this->em->transactional(function (EntityManager $em) use ($user) {
                $em->lock($user, LockMode::PESSIMISTIC_WRITE);

                do {
                    $inviteCode = $this->userService->generateInviteCode($user);
                    $exists = $this->userService->existsInviteCode($em, $inviteCode);
                } while ($exists);

                $user->setInviteCode($inviteCode);
                $em->persist($user);
                $em->flush();
            });

            $this->sendConfirmationMail($user);
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @return int
     */
    public function sendConfirmationMail(User $user)
    {
        $email = $user->getEmail();
        $hash = User::generateHash($user);

        try {
            $mailLogger = new Swift_Plugins_Loggers_ArrayLogger();
            $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($mailLogger));
            $message = (new Swift_Message('Gold Pillar | Confirmation instructions'))
                ->setFrom('lmercierhomologa@gmail.com')
                ->setTo($email)
                ->setBody($this->getConfirmationMailBody($email, $hash))
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
    private function getConfirmationMailBody($email, $hash)
    {
        $template = $this
            ->templating
            ->render('emails/subscribe/confirmation.html.twig', [
                'email' => $email,
                'hash' => $hash,
            ]);

        return $template;
    }

    public function getActiveSubscriptions()
    {
        /* @var $subscriptionRepository SubscriptionRepository */
        $subscriptionRepository = $this->em->getRepository(\App\Entity\Subscription::class);
        $subscriptions = $subscriptionRepository->getAllActive();

        return $subscriptions;
    }
}
