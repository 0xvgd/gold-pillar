<?php

namespace App\Service;

use App\Entity\Finance\EntryFee;
use App\Entity\Finance\Investment;
use App\Entity\Finance\Transaction;
use App\Entity\Person\Investor;
use App\Entity\Resource\InvestiblaInterface;
use App\Entity\Resource\Resource;
use App\Exception\InvestmentAmountExceededException;
use App\Exception\NonInvestibleResourceException;
use App\Service\Finance\AccountService;
use App\Service\Finance\InvestmentService;
use App\Service\Finance\TransactionService;
use Doctrine\ORM\EntityManagerInterface;

class InvestorService
{
    // XXX
    const INVESTMENT_FEE = .02;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var InvestmentService
     */
    private $investmentService;

    public function __construct(
        AccountService $accountService,
        TransactionService $transactionService,
        EntityManagerInterface $em,
        InvestmentService $investmentService
    ) {
        $this->accountService = $accountService;
        $this->transactionService = $transactionService;
        $this->em = $em;
        $this->investmentService = $investmentService;
    }

    public function makeInvestment(Investor $investor, Resource $resource, float $amount)
    {
        $return = null;

        if (!$resource instanceof InvestiblaInterface) {
            throw new NonInvestibleResourceException();
        }

        if ($resource->getMaxInvestmentValue() < $amount) {
            throw new InvestmentAmountExceededException();
        }

        $account = $resource->getAccount();

        $company = $resource->getCompany();
        $companyAccount = $resource->getCompany()->getAccount();

        if (!$companyAccount) {
            $companyAccount = $this
                ->accountService
                ->createCompanyAccount($company);
            $company->setAccount($companyAccount);
            $this->em->persist($company);
            $this->em->flush();
        }

        if (!$account) {
            $account = $this->accountService->createResourceAccount($resource);
        }

        $referenceCode = $this->investmentService->generateReferenceCode();

        $this
            ->transactionService
            ->requestDeposit(
                $investor->getUser(),
                $account,
                $amount,
                'Investment ref resource #'.$resource->getTag(),
                function (EntityManagerInterface $em, Transaction $transaction) use ($investor, $resource, $amount, &$return, $referenceCode) {
                    $investment = new Investment();

                    $investment
                        ->setReferenceCode($referenceCode)
                        ->setAmount($amount)
                        ->setFee(0)
                        ->setInvestor($investor)
                        ->setResource($resource)
                        ->setTransaction($transaction);

                    // incremeent total invested
                    $resource
                        ->getTotalInvested()
                        ->add($investment->getAmount());

                    $em->persist($investment);
                    $em->persist($resource);
                    $em->flush($investment);
                    $return['investment'] = $investment;
                }
            );

        $entryFeePerc = $resource->getCompany()->getSettings()->getAssetEntryFee();
        $feeAmount = $amount * $entryFeePerc;
        $this
            ->transactionService
            ->requestDeposit(
                $investor->getUser(),
                $companyAccount,
                $feeAmount,
                'Entry fee over '.$amount,
                function (EntityManagerInterface $em, Transaction $transaction) use ($investor, $resource, $feeAmount, $entryFeePerc, &$return, $referenceCode) {
                    $entryFee = new EntryFee();
                    $entryFee
                        ->setReferenceCode($referenceCode)
                        ->setAmount($feeAmount)
                        ->setFee($entryFeePerc)
                        ->setInvestor($investor)
                        ->setResource($resource)
                        ->setTransaction($transaction);

                    $em->persist($entryFee);
                    $em->flush($entryFee);
                    $return['entryFee'] = $entryFee;
                }
            );

        return $return;
    }
}
