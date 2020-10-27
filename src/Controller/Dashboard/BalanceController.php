<?php

namespace App\Controller\Dashboard;

use App\Service\BalanceService;
use App\Service\Finance\AccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/balance",
 *  name="dashboard_balance_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class BalanceController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(BalanceService $balanceService, AccountService $accountService)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $account = $user->getAccount();

        if (!$account) {
            $account = $accountService->createUserAccount($user);
        }

        return $this->render('dashboard/balance/index.html.twig', [
            'portalTransactions' => [],
            'userTransactions' => [],
            'userBalance' => $account->getBalance(),
            'portalBalance' => 0,
        ]);
    }
}
