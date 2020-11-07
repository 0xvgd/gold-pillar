<?php

namespace App\Controller\Traits;

use App\Entity\Security\User;
use App\Service\Finance\AccountService;
use Doctrine\ORM\EntityManagerInterface;

trait UserTrait
{
    protected function getUserAccount(
        User $user,
        EntityManagerInterface $em,
        AccountService $accountService
    ) {
        $userAccount = $this->getUser()->getAccount();

        if (!$userAccount) {
            $userAccount = $accountService->createUserAccount($user);
            $user->setAccount($userAccount);
            $em->persist($user);
            $em->flush();
        }

        return $this->getUser()->getAccount();
    }
}
