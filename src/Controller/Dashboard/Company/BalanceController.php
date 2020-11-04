<?php

namespace App\Controller\Dashboard\Company;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Finance\Transaction;
use App\Service\Company\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/company/balance",
 *  name="dashboard_company_balance_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class BalanceController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route("/", name="index")
     */
    public function index(
        Request $request,
        CompanyService $companyService
    ) {
        $company = $companyService->getDefaultCompany();
        $companyAccount = $company->getAccount();
        $balance = $companyAccount->getBalance();

        return $this->render('dashboard/company/balance/index.html.twig', [
            'balance' => $balance,
            'companyTransactions' => $companyAccount->getTransactions(),
        ]);
    }

    /**
     * @Route("/search.json", name="search", methods={"GET"})
     */
    public function search(
        Request $request,
        EntityManagerInterface $em,
        CompanyService $companyService
    ) {
        $company = $companyService->getDefaultCompany();
        $companyAccount = $company->getAccount();

        $qb = $em
            ->createQueryBuilder()
            ->select('e')
            ->from(Transaction::class, 'e')
            ->andWhere('e.account = :account')
            ->setParameter('account', $companyAccount)
            ->orderBy('e.id', 'DESC');

        $query = $qb->getQuery();

        return $this->dataTable($request, $query, false, [
            'groups' => 'list',
        ]);
    }
}
