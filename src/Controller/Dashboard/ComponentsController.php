<?php

namespace App\Controller\Dashboard;

use App\Entity\Finance\Account;
use App\Entity\Security\User;
use App\Service\Finance\AccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ComponentsController extends AbstractController
{
    public function userBalance(AccountService $service)
    {
        /** @var User $user */
        $user = $this->getUser();
        $account = $user->getAccount();

        if (!$account) {
            $account = $service->createUserAccount($user);
        }

        return $this->render('includes/agent-balance.html.twig', [
            'transactions' => [],
            'balance' => $account->getBalance(),
        ]);
    }

    public function accountBalance()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Account::class)->findOneBy([]);

        // TODO
        $balance = 0; //$em->getRepository(CompanyAccount::class)->getBalance($application);

        return $this->render('includes/account-balance.html.twig', [
            'entity' => $entity,
            'balance' => $balance,
        ]);
    }
}
