<?php

namespace App\Service;

use App\Entity\Security\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Hashids\Hashids;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

/**
 * UserService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class UserService
{
    private const MAX_INVITE_CODE_LENGTH = 10;
    private $em;
    private $mailer;
    private $templating;

    public function __construct(
        EntityManagerInterface $em,
        Swift_Mailer $mailer,
        Environment $templating
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @throws Exception
     */
    public function save(User $user)
    {
        $this->em->beginTransaction();
        try {
            $this->em->persist($user);
            $this->em->commit();
            $this->em->flush();
        } catch (Exception $e) {
            dd($e);
            $this->em->rollback();
            throw $e;
        }
    }

    /*
     * @param string $id
     */

    public function getUserById($id)
    {
        return $this->em->getRepository(User::class)->findOneBy([
            'id' => $id,
        ]);
    }

    /**
     * @param string $password
     *
     * @throws Exception
     */
    public function updatePasswordByUser(User $user, $password)
    {
        if ($user) {
            $user->setPassword($password);
            $user->setPasswordUpdateAt(new DateTime());
            $this->em->persist($user);
            $this->em->flush();
        } else {
            throw new Exception('Error updating password.');
        }

        return $user->getPassword()->getHash();
    }

    /**
     * @param string $email
     */
    public function recoveryPassword($email)
    {
        $user = $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email and u.confirmedAt is not null')
            ->setParameters([
                'email' => $email,
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($user) {
            $user->setUserHash($this->getHash($user->getEmail(), time()));
            $this->em->persist($user);
            $this->em->flush();
            $this->sendRecoveryPasswordMail($user->getEmail(), $user->getUserHash());
        } else {
            throw new Exception('E-mail not found.');
        }
    }

    /**
     * @param string $email
     * @param string $hash
     *
     * @return int
     */
    private function sendRecoveryPasswordMail($email, $hash)
    {
        try {
            $message = (new Swift_Message('Link para alterar senha'))
                ->setFrom('noreply@goldpillar.global')
                ->setTo($email)
                ->setBody($this->getRecoveryPasswordMail($hash))
                ->setContentType('text/html');

            $this->mailer->send($message);
        } catch (Exception $ex) {
            throw new Exception("Erro ao enviar notificação: {$ex->getMessage()}");
        }
    }

    /**
     * @param string $hash
     *
     * @return string
     */
    private function getRecoveryPasswordMail($hash)
    {
        return $this->templating
            ->render('emails/email_recovery_password.html.twig', [
                'hash' => $hash,
            ]);
    }

    /**
     * @param string $key
     * @param int    $salt
     *
     * @return string
     */
    private function getHash($key, $salt)
    {
        $hash = hash('sha256', $key.$salt);

        return $hash;
    }

    /**
     * @param string $hash
     */
    public function getByUserHash($hash)
    {
        $userRepository = $this->getRepository();

        return $userRepository->getByUserHash($hash);
    }

    /**
     * @param string $userHash
     */
    public function updatePasswordByUserHash($hash, $password)
    {
        $userRepository = $this->getRepository();

        /** @var User */
        $user = $userRepository->getByUserHash($hash);

        if ($user) {
            $user->setPassword($password);
            $user->setPasswordUpdateAt(new DateTime());
            $this->em->persist($user);
            $this->em->flush();
        } else {
            throw new Exception('Unable to update password.');
        }
    }

    /**
     * @return \App\Repository\UserRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(User::class);
    }

    public function existsInviteCode(EntityManagerInterface $em, string $inviteCode)
    {
        /** @var UserRepository $repo */
        $repo = $em->getRepository(User::class);
        $count = (int) $repo->count([
            'inviteCode' => $inviteCode,
        ]);
        $exists = $count > 0;

        return $exists;
    }

    public function generateInviteCode(User $user): string
    {
        $salt = sprintf('%s:%s', $user->getId(), rand());
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $hashids = new Hashids($salt, 5, $alphabet);
        $id = $hashids->encode($user->getCreatedAt()->getTimestamp());

        if (strlen($id) > self::MAX_INVITE_CODE_LENGTH) {
            $id = substr($id, 0, self::MAX_INVITE_CODE_LENGTH);
        }

        return $id;
    }
}
