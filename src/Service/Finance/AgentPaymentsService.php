<?php

namespace App\Service\Finance;

use App\Entity\ExecutiveClub;
use App\Entity\Finance\PropertyAccount;
use App\Entity\Helper\TableValue;
use App\Entity\Resource\Property;
use App\Entity\Security\Application;
use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Repository\ExecutiveClubRepository;
use App\Repository\Finance\PropertyAccountRepository;
use App\Repository\Sales\PropertyRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * AgentPaymentsService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class AgentPaymentsService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ParameterBagInterface */
    private $params;

    /** @var AccountService */
    private $accountService;

    public function __construct(
        EntityManagerInterface $em,
        ParameterBagInterface $params,
        AccountService $accountService
    ) {
        $this->em = $em;
        $this->params = $params;
        $this->accountService = $accountService;
    }

    /**
     * @param DateTimeInterface $dueDate
     */
    public function monthlyFeeGenerate(
        Subscription $subscription,
        float $amount,
        int $monthRef,
        int $yearRef,
        $dueDate
    ) {
        $transactionType = $this->em
            ->getRepository(TableValue::class)
            ->find(TableValue::TRANSACTION_TYPE_AGENT_MONTHLY_FEE);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setType($transactionType);
        $transaction->setDescription('Agent monthly fee');
        $transaction->setMonthRef($monthRef);
        $transaction->setYearRef($yearRef);
        $transaction->setDueDate($dueDate);
        $transaction->setUser($subscription->getUser());

        $this->em->persist($transaction);
        $this->em->flush();
    }

    public function getTotalSalesByUser($user)
    {
        /** @var PropertyRepository */
        $propertyRepository = $this->em->getRepository(Property::class);
        $totalSales = $propertyRepository->getTotalSalesByUser($user);

        return $totalSales;
    }

    public function getTotalSalesByMonth($user, $month)
    {
        /** @var PropertyRepository */
        $propertyRepository = $this->em->getRepository(Property::class);
        $totalSalesByMonth = $propertyRepository->getTotalSalesByMonth($user, $month);

        return $totalSalesByMonth;
    }

    /**
     * @param DateTimeInterface $dueDate
     */
    public function commissionGenerate(
        Property $property
    ) {
        if ($property->getSoldAt()) {
            return null;
        }

        $this->em->beginTransaction();

        $transactionId = md5(uniqid(time(), true));
        $globalTransactionId = md5(uniqid(time(), true));

        try {
            $companyCommissionTransaction = $this->em
                ->getRepository(TableValue::class)
                ->find(TableValue::TRANSACTION_TYPE_TOTAL_COMISSION);

            $companyCommissionType = $this->em
                ->getRepository(TableValue::class)
                ->find(TableValue::TRANSACTION_TYPE_COMPANY_COMISSION);

            $agentCommissionTransaction = $this->em
                ->getRepository(TableValue::class)
                ->find(TableValue::TRANSACTION_TYPE_AGENT_COMISSION);

            $parentAgentBonus = $this->em
                ->getRepository(TableValue::class)
                ->find(TableValue::TRANSACTION_TYPE_PARENT_AGENT_BONUS);

            $grandparentAgentBonus = $this->em
                ->getRepository(TableValue::class)
                ->find(TableValue::TRANSACTION_TYPE_GRANDPARENT_AGENT_BONUS);

            $propertyUndistributedAmount = $this->em
                ->getRepository(TableValue::class)
                ->find(TableValue::PROPERTY_TRANSACTION_TYPE_UNDISTRIBUTED_AMOUNT);

            //add 1 to total sales to count current sale
            $totalSales = $this->getTotalSalesByUser($property->getOwner()) + 1;
            $executiveClub = $this->getExecutiveClubLevel($totalSales);
            $price = $property->getPrice()->getAmount();
            $commissionRate = $property->getCommissionRate();
            $totalCommission = $price * $commissionRate;

            $transaction = [
                'amount' => $totalCommission,
                'description' => 'Commission rate',
                'transactionObjectId' => $property->getId(),
                'transactionObjectClass' => Property::class,
                'type' => $companyCommissionTransaction,
                'entity' => $property,
                'fee' => $commissionRate,
                'refValue' => $price,
                'globalTransactionId' => $globalTransactionId,
                'transactionId' => $transactionId,
                'dateRef' => null,
            ];

            //adiciona a comissão total na conta da propriedade
            $this->propertyAccountService->doTransaction($transaction, $this->em);

            $agentCommission = $totalCommission * $executiveClub->getFee();
            $agentAccount = $this->userAccountService->getUserAccount($property->getUser());

            $transactionId = md5(uniqid(time(), true));
            $transaction = [
                'amount' => $agentCommission,
                'description' => 'Agent commission',
                'entity' => $property,
                'user' => $property->getAgent()->getUser(),
                'type' => $agentCommissionTransaction,
                'account' => $agentAccount,
                'fee' => $executiveClub->getFee(),
                'refValue' => $totalCommission,
                'globalTransactionId' => $globalTransactionId,
                'transactionId' => $transactionId,
                'dateRef' => null,
            ];

            //remunera o agent
            $this->propertyAccountService->doTransaction($transaction, $this->em, true);
            $this->userAccountService->doTransaction($transaction, $this->em);

            //remunera a empresa

            /** @var PropertyAccountRepository */
            $repo = $this->em->getRepository(PropertyAccount::class);

            $companyCommission = $totalCommission * $executiveClub->getCompanyFee();

            $transactionId = md5(uniqid(time(), true));
            $transaction = [
                'amount' => $companyCommission,
                'description' => 'Company commission',
                'transactionObjectId' => $property->getId(),
                'transactionObjectClass' => Property::class,
                'type' => $companyCommissionType,
                'entity' => $property,
                'fee' => $executiveClub->getCompanyFee(),
                'refValue' => $totalCommission,
                'globalTransactionId' => $globalTransactionId,
                'transactionId' => $transactionId,
                'dateRef' => null,
            ];

            $this->propertyAccountService->doTransaction($transaction, $this->em, true);
            $this->companyAccountService->doTransaction($transaction, $this->em);

            /** @var PropertyAccountRepository */
            $repo = $this->em->getRepository(PropertyAccount::class);

            $companyCommission = $totalCommission * $executiveClub->getCompanyFee();
            $distributableValue = $totalCommission * $executiveClub->getDistributableValue();
            $companyCommission = $distributableValue * 0.10;

            $transactionId = md5(uniqid(time(), true));
            $transaction = [
                'amount' => $companyCommission,
                'description' => 'Company commission',
                'transactionObjectId' => $property->getId(),
                'transactionObjectClass' => Property::class,
                'type' => $companyCommissionType,
                'entity' => $property,
                'fee' => 0.10,
                'refValue' => $distributableValue,
                'globalTransactionId' => $globalTransactionId,
                'transactionId' => $transactionId,
                'dateRef' => null,
            ];

            $this->propertyAccountService->doTransaction($transaction, $this->em, true);
            $this->companyAccountService->doTransaction($transaction, $this->em);

            //remunera o parent user 1
            if ($parentUser = $property->getAgent()->getUser()->getParentUser()) {
                $parentCommission = $distributableValue * 0.70;
                $parentAccount = $this->userAccountService->getUserAccount(
                    $parentUser
                );

                $transactionId = md5(uniqid(time(), true));
                $transaction = [
                    'amount' => $parentCommission,
                    'description' => 'Parent I commission',
                    'entity' => $property,
                    'user' => $parentUser,
                    'type' => $parentAgentBonus,
                    'account' => $parentAccount,
                    'fee' => 0.70,
                    'refValue' => $distributableValue,
                    'globalTransactionId' => $globalTransactionId,
                    'transactionId' => $transactionId,
                    'dateRef' => null,
                ];

                $this->propertyAccountService->doTransaction($transaction, $this->em, true);
                $this->userAccountService->doTransaction($transaction, $this->em);
            }

            //remunera o parent user 2
            if ($grandParentUser = $property->getAgent()->getUser()->getParentUser()->getParentUser()) {
                $grandparentCommission = $distributableValue * 0.20;

                $parentAccount = $this->userAccountService->getUserAccount(
                    $grandParentUser
                );

                $transactionId = md5(uniqid(time(), true));
                $transaction = [
                    'amount' => $grandparentCommission,
                    'description' => 'Parent II commission',
                    'entity' => $property,
                    'user' => $grandParentUser,
                    'type' => $grandparentAgentBonus,
                    'account' => $parentAccount,
                    'fee' => 0.20,
                    'refValue' => $distributableValue,
                    'globalTransactionId' => $globalTransactionId,
                    'transactionId' => $transactionId,
                    'dateRef' => null,
                ];

                $this->propertyAccountService->doTransaction($transaction, $this->em, true);
                $this->userAccountService->doTransaction($transaction, $this->em);
            }

            /** @var PropertyAccountRepository */
            $propertyAcountRepo = $this->em->getRepository(PropertyAccount::class);
            $balance = $propertyAcountRepo->getBalance($property);

            $transactionId = md5(uniqid(time(), true));
            $transaction = [
                'amount' => $balance,
                'description' => 'Property undistributed amount',
                'transactionObjectId' => $property->getId(),
                'transactionObjectClass' => Property::class,
                'type' => $propertyUndistributedAmount,
                'entity' => $property,
                'fee' => 0,
                'refValue' => 0,
                'globalTransactionId' => $globalTransactionId,
                'transactionId' => $transactionId,
                'dateRef' => null,
            ];

            $this->propertyAccountService->doTransaction($transaction, $this->em, true);
            $this->companyAccountService->doTransaction($transaction, $this->em);

            $property->setSoldAt(new DateTime('now'));

            $this->em->persist($property);
            $this->em->flush();

            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @return Application
     */
    private function getCurrentApplication()
    {
        $appName = $this->params->get('app.name');
        $application = $this->em->getRepository(Application::class)->findOneBy([
            'name' => $appName,
        ]);

        return $application;
    }

    /**
     * @return ExecutiveClub
     */
    private function getExecutiveClubLevel($numberOfSales)
    {
        /** @var ExecutiveClubRepository */
        $executiveClubRepository = $this->em->getRepository(ExecutiveClub::class);

        return $executiveClubRepository->findByNumberOfSales($numberOfSales);
    }
}
