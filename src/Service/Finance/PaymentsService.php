<?php

namespace App\Service\Finance;

use App\Entity\Company\Company;
use App\Entity\ExecutiveClub;
use App\Entity\Finance\Account;
use App\Entity\Finance\CapitalReturn;
use App\Entity\Finance\CapitalReturnPayment;
use App\Entity\Finance\Commission;
use App\Entity\Finance\CommissionPayment;
use App\Entity\Finance\Dividend;
use App\Entity\Finance\DividendPayment;
use App\Entity\Finance\ExpensePayment;
use App\Entity\Finance\IncomePayment;
use App\Entity\Finance\Investment;
use App\Entity\Finance\Profit;
use App\Entity\Finance\ProfitPayment;
use App\Entity\Finance\Recurring;
use App\Entity\Finance\RecurringExpense;
use App\Entity\Finance\RecurringIncome;
use App\Entity\Finance\Transaction;
use App\Entity\InvestorProfit;
use App\Entity\Person\Investor;
use App\Entity\Resource\Asset;
use App\Entity\Resource\InvestiblaInterface;
use App\Entity\Resource\Project;
use App\Entity\Resource\Property;
use App\Entity\Resource\Resource;
use App\Entity\Security\User;
use App\Enum\PeriodUnit;
use App\Exception\InsufficientBalanceException;
use App\Exception\NonInvestibleResourceException;
use App\Repository\ExecutiveClubRepository;
use App\Repository\InvestorProfitRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PaymentsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $em,
        AccountService $accountService,
        TransactionService $transactionService,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->accountService = $accountService;
        $this->transactionService = $transactionService;
        $this->tokenStorage = $tokenStorage;
    }

    public function distributeDividend(
        User $user,
        Asset $resource,
        DateTime $exDate
    ) {
        if (!$resource instanceof InvestiblaInterface) {
            throw new NonInvestibleResourceException();
        }

        $this->em->transactional(function (EntityManagerInterface $em) use (
            $user,
            $resource,
            $exDate
        ) {
            $account = $resource->getAccount();
            $totalInvested = $resource->getTotalInvested()->getAmount();
            $amount = $account->getBalance() - $totalInvested;
            $equity = $resource
                ->getLastEquity()
                ->getPrice()
                ->getAmount();
            $yield = $amount / $equity;

            if ($amount < 0) {
                throw new InsufficientBalanceException();
            }

            $investors = $em
                ->createQueryBuilder()
                ->select('e', 'i')
                ->from(Investor::class, 'e')
                ->join('e.investments', 'i')
                ->where('i.resource = :resource')
                ->andWhere('i.createdAt <= :exDate')
                ->setParameters([
                    'resource' => $resource,
                    'exDate' => $exDate,
                ])
                ->getQuery()
                ->setLockMode(LockMode::PESSIMISTIC_READ)
                ->getResult();

            $count = count($investors);

            if (0 === $count) {
                throw new Exception('No one investments found due ex-date.');
            }

            $dividend = new Dividend();
            $dividend
                ->setResource($resource)
                ->setAmount($amount)
                ->setYield($yield)
                ->setExDate($exDate)
                ->setInvestorsCount($count);

            $em->persist($dividend);
            $em->flush();

            foreach ($investors as $investor) {
                $individualInvested = 0;
                /** @var Investment $investment */
                foreach ($investor->getInvestments() as $investment) {
                    $individualInvested += $investment->getAmount();
                }
                if ($individualInvested > 0) {
                    $investorAccount = $investor->getUser()->getAccount();
                    if (!$investorAccount) {
                        $investorAccount = $this->accountService->createUserAccount(
                            $investor->getUser()
                        );
                    }

                    $dividendAmount = $individualInvested * $yield;
                    $note = 'Dividend ref resource #'.$resource->getTag();

                    $this->transactionService->requestTransfer(
                        $user,
                        $account,
                        $investorAccount,
                        $dividendAmount,
                        $note,
                        function (
                            EntityManagerInterface $em,
                            Transaction $transaction
                        ) use ($resource, $dividend, $note) {
                            $payment = new DividendPayment($resource);
                            $payment
                                ->setDividend($dividend)
                                ->setAmount($transaction->getAmount())
                                ->setNote($note)
                                ->setTransaction($transaction);

                            $em->persist($payment);
                            $em->flush();

                            $this->transactionService->process($transaction);
                        }
                    );
                }
            }
        });
    }

    public function distributeProfit(
        User $user,
        Project $resource,
        DateTime $exDate
    ) {
        if (!$resource instanceof InvestiblaInterface) {
            throw new NonInvestibleResourceException();
        }

        $this->em->transactional(function (EntityManagerInterface $em) use (
            $user,
            $resource,
            $exDate
        ) {
            $account = $resource->getAccount();
            $totalInvested = $resource->getTotalInvested()->getAmount();
            $projectProfit = $account->getBalance() - $totalInvested;
            $roi = $resource->getRoi();

            if ($projectProfit < 0) {
                throw new InsufficientBalanceException();
            }

            $investors = $em
                ->createQueryBuilder()
                ->select('e', 'i')
                ->from(Investor::class, 'e')
                ->join('e.investments', 'i')
                ->where('i.resource = :resource')
                ->andWhere('i.createdAt <= :exDate')
                ->setParameters([
                    'resource' => $resource,
                    'exDate' => $exDate,
                ])
                ->getQuery()
                ->setLockMode(LockMode::PESSIMISTIC_READ)
                ->getResult();

            $count = count($investors);

            if (0 === $count) {
                throw new Exception('No one investments found due ex-date.');
            }

            $profit = new Profit();
            $profit
                ->setResource($resource)
                ->setAmount($projectProfit)
                ->setRoi($roi)
                ->setExDate($exDate)
                ->setInvestorsCount($count);

            $em->persist($profit);
            $em->flush();

            $capitalReturn = new CapitalReturn();
            $capitalReturn
                ->setResource($resource)
                ->setAmount($totalInvested)
                ->setExDate($exDate)
                ->setInvestorsCount($count);

            foreach ($investors as $investor) {
                $individualInvested = 0;
                /** @var Investment $investment */
                foreach ($investor->getInvestments() as $investment) {
                    if (
                        $investment->getResource()->getId() ===
                        $resource->getId()
                    ) {
                        $individualInvested += $investment->getAmount();
                    }
                }

                if ($individualInvested > 0) {
                    $investorAccount = $investor->getUser()->getAccount();
                    if (!$investorAccount) {
                        $investorAccount = $this->accountService->createUserAccount(
                            $investor->getUser()
                        );
                        $investor->getUser()->setAccount($investorAccount);
                        $em->persist($investor);
                        $em->flush();
                    }

                    $investorProfit = $this->getInvestorProfit(
                        $individualInvested,
                        $resource->getCompany()
                    );

                    $investmentPercentage =
                        $individualInvested /
                        $resource->getPurchasePrice()->getAmount();
                    $proportionalAmount =
                        $profit->getAmount() * $investmentPercentage;
                    $userProfitAmount =
                        $proportionalAmount * $investorProfit->getProfit();

                    $note = 'Return of capital';
                    $this->transactionService->requestTransfer(
                        $user,
                        $account,
                        $investorAccount,
                        $individualInvested,
                        $note,
                        function (
                            EntityManagerInterface $em,
                            Transaction $transaction
                        ) use ($resource, $capitalReturn, $note) {
                            $payment = new CapitalReturnPayment($resource);
                            $payment
                                ->setCapitalReturn($capitalReturn)
                                ->setAmount($transaction->getAmount())
                                ->setNote($note)
                                ->setTransaction($transaction);

                            $em->persist($payment);
                            $em->flush();

                            $this->transactionService->process($transaction);
                        }
                    );

                    $note = 'Profit ref resource #'.$resource->getTag();
                    $this->transactionService->requestTransfer(
                        $user,
                        $account,
                        $investorAccount,
                        $userProfitAmount,
                        $note,
                        function (
                            EntityManagerInterface $em,
                            Transaction $transaction
                        ) use ($resource, $profit, $note) {
                            $payment = new ProfitPayment($resource);
                            $payment
                                ->setProfit($profit)
                                ->setAmount($transaction->getAmount())
                                ->setNote($note)
                                ->setTransaction($transaction);

                            $em->persist($payment);
                            $em->flush();

                            $this->transactionService->process($transaction);
                        }
                    );

                    $fee = 0.8 - $investorProfit->getProfit();
                    $this->companyCommissionPayment(
                        $resource,
                        $em,
                        $account,
                        $proportionalAmount,
                        $fee,
                        'Company commission'
                    );
                }
            }

            $settings = $resource->getCompany()->getSettings();
            $fee = $settings->getProjectAdvertisingReserve();
            $this->companyCommissionPayment(
                $resource,
                $em,
                $account,
                $profit->getAmount(),
                $fee,
                'Advertising reserve'
            );

            if ($resource->getBroker()) {
                $fee = $settings->getProjectBrokerEarnings();
                $user = $resource->getBroker()->getUser();
                $this->userCommissionPayment(
                    $resource,
                    $em,
                    $account,
                    $profit->getAmount(),
                    $fee,
                    $user,
                    'Broker commission'
                );
            }

            if ($resource->getEngineer()) {
                $fee = $settings->getProjectEngineerEarnings();
                $user = $resource->getEngineer()->getUser();
                $this->userCommissionPayment(
                    $resource,
                    $em,
                    $account,
                    $profit->getAmount(),
                    $fee,
                    $user,
                    'Engineer commission'
                );
            }

            if ($resource->getContractor()) {
                $fee = $settings->getProjectContractorEarnings();
                $user = $resource->getContractor()->getUser();
                $this->userCommissionPayment(
                    $resource,
                    $em,
                    $account,
                    $profit->getAmount(),
                    $fee,
                    $user,
                    'Contractor commission'
                );
            }

            if ($resource->getAgent()) {
                $fee = $settings->getProjectAgentEarnings();
                $user = $resource->getAgent()->getUser();
                $this->userCommissionPayment(
                    $resource,
                    $em,
                    $account,
                    $profit->getAmount(),
                    $fee,
                    $user,
                    'Agent commission'
                );
            }
        });
    }

    public function distributeCommission(
        User $user,
        Property $resource,
        DateTime $exDate
    ) {
        $this->em->transactional(function (EntityManagerInterface $em) use (
            $user,
            $resource
        ) {
            $settings = $resource->getCompany()->getSettings();
            $account = $resource->getAccount();
            $totalCommission = $account->getBalance();
            $totalSales = $this->getTotalSalesByUser($resource->getOwner()) + 1;
            $executiveClub = $this->getExecutiveClubLevel($totalSales);
            $commissionRate = $resource->getCommissionRate();

            if (floatval($totalCommission) <= 0) {
                throw new InsufficientBalanceException();
            }

            $user = $resource->getAgent()->getUser();

            $this->userCommissionPayment(
                $resource,
                $em,
                $account,
                $totalCommission,
                $executiveClub->getFee(),
                $user,
                'Agent commission'
            );

            $this->companyCommissionPayment(
                $resource,
                $em,
                $account,
                $totalCommission,
                $executiveClub->getCompanyFee(),
                'Company commission'
            );

            $distributableValue =
                $totalCommission * $executiveClub->getDistributableValue();

            if ($resource->getAgent() && floatval($account->getBalance()) > 0) {
                $agentUser = $resource->getAgent()->getUser();
                if ($parentUser = $agentUser->getParentUser()) {
                    $this->userCommissionPayment(
                        $resource,
                        $em,
                        $account,
                        $distributableValue,
                        0.7,
                        $parentUser,
                        'Agent level 1 commission'
                    );

                    //remunera o parent user 2
                    if ($parentUser->getParentUser()) {
                        $this->userCommissionPayment(
                            $resource,
                            $em,
                            $account,
                            $distributableValue,
                            0.2,
                            $parentUser->getParentUser(),
                            'Agent level 2 commission'
                        );
                    }
                }
            }
        });
    }

    private function userCommissionPayment(
        Resource $resource,
        EntityManagerInterface $em,
        Account $account,
        $amount,
        $fee,
        User $user,
        string $note
    ) {
        if ($user) {
            $userAccount = $user->getAccount();
            if (!$userAccount) {
                $userAccount = $this->accountService->createUserAccount($user);
                $user->setAccount($userAccount);
                $em->persist($user);
                $em->flush();
            }

            $this->commissionPayment(
                $user,
                $resource,
                $account,
                $amount,
                $fee,
                $user->getAccount(),
                $note
            );
        }
    }

    private function companyCommissionPayment(
        Resource $resource,
        EntityManagerInterface $em,
        Account $account,
        $amount,
        $fee,
        string $note
    ) {
        $company = $resource->getCompany();
        $companyAccount = $company->getAccount();
        if (!$companyAccount) {
            $companyAccount = $this->accountService->createCompanyAccount(
                $resource->getCompany()
            );
            $company->setAccount($companyAccount);
            $em->persist($company);
            $em->flush();
        }

        $this->commissionPayment(
            $this->getCurrentUser(),
            $resource,
            $account,
            $amount,
            $fee,
            $company->getAccount(),
            $note
        );
    }

    private function commissionPayment(
        User $user,
        Resource $resource,
        Account $originAccount,
        $amount,
        $fee,
        Account $destinyAccount,
        string $note
    ) {
        $this->transactionService->requestTransfer(
            $user,
            $originAccount,
            $destinyAccount,
            $amount * $fee,
            $note,
            function (
                EntityManagerInterface $em,
                Transaction $transaction
            ) use ($resource, $amount, $note, $fee) {
                $commission = new Commission();
                $commission
                    ->setResource($resource)
                    ->setAmount($amount * $fee)
                    ->setRefAmount($amount)
                    ->setFee($fee);

                $payment = new CommissionPayment($resource);
                $payment
                    ->setCommission($commission)
                    ->setAmount($transaction->getAmount())
                    ->setNote($note)
                    ->setTransaction($transaction);

                $em->persist($payment);
                $em->flush();

                $this->transactionService->process($transaction);
            }
        );
    }

    public function saveRecurringIncome(
        Resource $resource,
        RecurringIncome $recurring
    ) {
        $recurring->setResource($resource);
        $this->saveRecurring($resource->getRecurringIncomes(), $recurring);
    }

    public function saveRecurringExpense(
        Resource $resource,
        RecurringExpense $recurring
    ) {
        $recurring->setResource($resource);
        $this->saveRecurring($resource->getRecurringExpenses(), $recurring);
    }

    public function removeRecurringIncome(
        Resource $resource,
        RecurringIncome $recurring
    ) {
        $recurring->setResource($resource);
        $this->removeRecurring($resource->getRecurringIncomes(), $recurring);
    }

    public function removeRecurringExpense(
        Resource $resource,
        RecurringExpense $recurring
    ) {
        $recurring->setResource($resource);
        $this->removeRecurring($resource->getRecurringExpenses(), $recurring);
    }

    private function saveRecurring(
        Collection $recurringCollection,
        Recurring $recurring
    ) {
        $yearlyProjection = $this->getYearlyProjection($recurring);

        $recurring->setYearlyProjection($yearlyProjection);

        if (!$recurringCollection->contains($recurring)) {
            $recurringCollection->add($recurring);
        }

        $resource = $recurring->getResource();
        $this->updateYearlyProjection($resource, $recurringCollection);

        $this->em->persist($recurring);
        $this->em->flush();
    }

    private function removeRecurring(
        Collection $recurringCollection,
        Recurring $recurring
    ) {
        if ($recurringCollection->contains($recurring)) {
            $recurringCollection->removeElement($recurring);
        }

        $resource = $recurring->getResource();
        $this->updateYearlyProjection($resource, $recurringCollection);

        $this->em->remove($recurring);
        $this->em->flush();
    }

    private function updateYearlyProjection(
        Resource $resource,
        Collection $recurringCollection
    ) {
        $totalYearlyProjection = $this->getYearlyProjection(
            ...$recurringCollection
        );
        $resource
            ->setYearlyIncome($totalYearlyProjection)
            ->setMonthlyIncome($totalYearlyProjection / 12);

        $this->em->persist($resource);
    }

    public function addIncome(
        User $user,
        IncomePayment $payment,
        bool $process,
        callable $fn = null
    ) {
        $this->transactionService->requestDeposit(
            $user,
            $payment->getResource()->getAccount(),
            $payment->getAmount(),
            $payment->getNote(),
            function (
                EntityManagerInterface $em,
                Transaction $transaction
            ) use ($payment, $process, $fn) {
                $payment->setTransaction($transaction);
                $em->persist($payment);
                $em->flush();

                if ($process) {
                    $this->transactionService->process($transaction);
                }

                if (is_callable($fn)) {
                    $fn($payment);
                }
            }
        );
    }

    public function addExpense(
        User $user,
        ExpensePayment $payment,
        bool $process,
        callable $fn = null
    ) {
        $this->transactionService->requestWithdrawal(
            $user,
            $payment->getResource()->getAccount(),
            $payment->getAmount(),
            $payment->getNote(),
            function (
                EntityManagerInterface $em,
                Transaction $transaction
            ) use ($payment, $process, $fn) {
                $payment->setTransaction($transaction);
                $em->persist($payment);
                $em->flush();

                if ($process) {
                    $this->transactionService->process($transaction);
                }

                if (is_callable($fn)) {
                    $fn($payment);
                }
            }
        );
    }

    public function isLeapYear(int $year = null): bool
    {
        if (null === $year) {
            $year = (int) date('Y');
        }

        return date('L', strtotime("$year-01-01")) ? true : false;
    }

    public function getYearlyIncomeProjection(Resource $resource): float
    {
        $amount = $this->getYearlyProjection(
            ...$resource->getRecurringIncomes()
        );

        return $amount;
    }

    public function getYearlyExpenseProjection(Resource $resource): float
    {
        $amount = $this->getYearlyProjection(
            ...$resource->getRecurringExpenses()
        );

        return $amount;
    }

    public function getYearlyProjection(Recurring ...$recurring): float
    {
        $amount = 0;

        foreach ($recurring as $r) {
            switch ($r->getInterval()->getValue()) {
                case PeriodUnit::DAY():
                    $days = $this->isLeapYear() ? 366 : 365;
                    $amount += $r->getAmount() * $days;
                    break;
                case PeriodUnit::WEEK():
                    $amount += $r->getAmount() * 52;
                    break;
                case PeriodUnit::MONTH():
                    $amount += $r->getAmount() * 12;
                    break;
                case PeriodUnit::YEAR():
                    $amount += $r->getAmount() * 1;
                    break;
            }
        }

        return $amount;
    }

    public function getProfitByProject(Project $project)
    {
        $profit =
            $project->getSaleRealPrice() -
            $project->getPurchasePrice() -
            $project->getConstructionCost();

        return $profit;
    }

    /**
     * @return InvestorProfit
     */
    private function getInvestorProfit($investment, Company $company)
    {
        /** @var InvestorProfitRepository */
        $investorProfitRepository = $this->em->getRepository(
            InvestorProfit::class
        );
        $investorProfit = $investorProfitRepository->findByInvestmentValue(
            $investment,
            $company
        );

        return $investorProfit;
    }

    protected function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        $user = null;
        if ($token) {
            $user = $token->getUser();
        }

        return $user;
    }

    public function getTotalSalesByUser($user)
    {
        /** @var PropertyRepository */
        $propertyRepository = $this->em->getRepository(Property::class);
        $totalSales = $propertyRepository->getTotalSalesByUser($user);

        return $totalSales;
    }

    /**
     * @return ExecutiveClub
     */
    private function getExecutiveClubLevel($numberOfSales)
    {
        /** @var ExecutiveClubRepository */
        $executiveClubRepository = $this->em->getRepository(
            ExecutiveClub::class
        );

        return $executiveClubRepository->findByNumberOfSales($numberOfSales);
    }
}
