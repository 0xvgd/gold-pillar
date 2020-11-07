<?php

namespace App\Controller\Dashboard\MyFinance;

use App\Controller\Traits\DatatableTrait;
use App\Controller\Traits\UserTrait;
use App\Entity\Finance\CapitalReturnPayment;
use App\Entity\Finance\Commission;
use App\Entity\Finance\CommissionPayment;
use App\Entity\Finance\Investment;
use App\Entity\Finance\ProfitPayment;
use App\Entity\Finance\Transaction;
use App\Entity\Person\Agent;
use App\Entity\Person\Investor;
use App\Entity\Resource\Project;
use App\Entity\Resource\Property;
use App\Enum\InvestmentStatus;
use App\Service\Finance\AccountService;
use App\Service\Finance\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/my/finance/properties",
 *  name="dashboard_my_finance_properties_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class PropertiesController extends AbstractController
{
    use DatatableTrait;
    use UserTrait;
    /**
     * @Route("/", name="index")
     */
    public function index(
        EntityManagerInterface $em,
        AccountService $accountService
    ) {
        $userAccount = $this->getUserAccount(
            $this->getUser(),
            $em,
            $accountService
        );

        $balance = $userAccount->getBalance();

        return $this->render(
            'dashboard/my_finance/properties/index.html.twig',
            [
                'balance' => $balance,
                'companyTransactions' => $userAccount->getTransactions(),
            ]
        );
    }

    /**
     * @Route("/search_commission.json", name="search_commission", methods={"GET"})
     */
    public function searchComission(
        Request $request,
        EntityManagerInterface $em,
        AccountService $accountService
    ) {
        $user = $this->getUser();

        $search = $request->get('search');

        if (!is_array($search)) {
            $search = [
                'basic' => [],
            ];
        }

        $search = $this->searchValues($request);

        /** @var Investor */
        $agent = $em->getRepository(Agent::class)->findOneBy(['user' => $user]);

        if (!$agent) {
            return $this->redirectToRoute('dashboard_index');
        }

        $userAccount = $this->getUserAccount(
            $this->getUser(),
            $em,
            $accountService
        );

        $qb = $em
            ->createQueryBuilder()
            ->select(['e'])
            ->from(CommissionPayment::class, 'e')
            ->join('e.transaction', 't')
            ->join('t.user', 'u')
            ->join('t.account', 'ac')
            ->join('e.resource', 'r')
            ->andWhere('u= :user')
            ->andWhere(sprintf('r INSTANCE OF %s', Property::class))
            ->orderBy('e.id', 'desc')
            ->setParameters([
                'user' => $user,
            ]);

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => ['list'],
        ]);
    }

    /**
     * @Route("/{id}", name="view", methods={"GET"})
     */
    public function view(Request $request, Investment $investment)
    {
        return $this->render('dashboard/my_finance/properties/view.html.twig', [
            'entity' => $investment,
        ]);
    }

    /**
     * @Route("/{id}/transaction", name="transaction", methods={"POST"})
     */
    public function justToReturn(Investment $investment)
    {
        // if the form was submitted without inform _method (PUT or DELETE), just redirect to view
        return $this->redirectToRoute('dashboard_properties_investments_view', [
            'id' => $investment->getId(),
        ]);
    }

    /**
     * @Route("/{id}/transaction", methods={"PUT"})
     */
    public function confirmTransaction(
        TransactionService $service,
        Investment $investment
    ) {
        $service->process($investment->getTransaction(), function (
            EntityManagerInterface $em,
            Transaction $transaction
        ) use ($investment) {
            $investment
                ->setApprovedAt($transaction->getProcessedAt())
                ->setStatus(InvestmentStatus::APPROVED());

            $em->persist($investment);
            $em->flush();
        });

        return $this->redirectToRoute('dashboard_properties_investments_view', [
            'id' => $investment->getId(),
        ]);
    }

    /**
     * @Route("/{id}/transaction", methods={"DELETE"})
     */
    public function cancelTransaction(
        TransactionService $service,
        Investment $investment
    ) {
        $service->cancel($investment->getTransaction(), function (
            EntityManagerInterface $em,
            Transaction $transaction
        ) use ($investment) {
            $investment
                ->setApprovedAt($transaction->getCancelledAt())
                ->setStatus(InvestmentStatus::DENIED());

            // reenable investment amount
            $resource = $investment->getResource();
            $resource->getTotalInvested()->sub($investment->getAmount());

            $em->persist($investment);
            $em->persist($resource);
            $em->flush();
        });

        return $this->redirectToRoute('dashboard_properties_investments_view', [
            'id' => $investment->getId(),
        ]);
    }
}
