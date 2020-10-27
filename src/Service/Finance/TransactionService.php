<?php

namespace App\Service\Finance;

use App\Entity\Finance\Account;
use App\Entity\Finance\DepositTransaction;
use App\Entity\Finance\Transaction;
use App\Entity\Finance\TransferTransaction;
use App\Entity\Finance\WithdrawalTransaction;
use App\Entity\Security\User;
use App\Enum\TransactionStatus;
use App\Exception\InsufficientBalanceException;
use App\Repository\TransactionRepository;
use DateTime;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Hashids\Hashids;

class TransactionService
{
    private const MAX_TRANSACTION_ID_LENGTH = 10;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function requestDeposit(
        User $user,
        Account $account,
        float $amount,
        string $note,
        callable $fn = null
    ): DepositTransaction {
        return $this->em->transactional(function (EntityManager $em) use ($user, $account, $amount, $note, $fn) {
            $this->em->lock($account, LockMode::PESSIMISTIC_WRITE);

            do {
                $transaction = $this->createDepositTransaction($user, $account, $amount, $note);

                $exists = $this->existsTransactionId($em, $transaction);
            } while ($exists);

            $this->addTransaction($em, $transaction);

            if (is_callable($fn)) {
                $fn($em, $transaction);
            }

            return $transaction;
        });
    }

    public function requestWithdrawal(
        User $user,
        Account $account,
        float $amount,
        string $note,
        callable $fn = null
    ): WithdrawalTransaction {
        if ($account->getBalance() < $amount) {
            throw new InsufficientBalanceException();
        }

        return $this->em->transactional(function (EntityManager $em) use ($user, $account, $amount, $note, $fn) {
            $em->lock($account, LockMode::PESSIMISTIC_WRITE);

            do {
                $transaction = $this->createWithdrawalTransaction($user, $account, $amount, $note);
                $exists = $this->existsTransactionId($em, $transaction);
            } while ($exists);

            $this->addTransaction($em, $transaction);

            if (is_callable($fn)) {
                $fn($em, $transaction);
            }

            return $transaction;
        });
    }

    public function requestTransfer(
        User $user,
        Account $origin,
        Account $destiny,
        float $amount,
        string $note,
        callable $fn = null
    ): TransferTransaction {
        if ($origin->getBalance() < $amount) {
            throw new InsufficientBalanceException();
        }

        return $this->em->transactional(function (EntityManager $em) use ($user, $origin, $destiny, $amount, $note, $fn) {
            // lock order to prevent deadlock
            if ($origin->getCreatedAt() < $destiny->getCreatedAt()) {
                $firstToLock = $origin;
                $secondToLock = $destiny;
            } else {
                $firstToLock = $destiny;
                $secondToLock = $origin;
            }

            $em->lock($firstToLock, LockMode::PESSIMISTIC_WRITE);
            $em->lock($secondToLock, LockMode::PESSIMISTIC_WRITE);

            do {
                $exists = false;
                $transaction = $this->createTransferTransaction($user, $origin, $destiny, $amount, $note);
                $exists = $this->existsTransactionId($em, $transaction);
                if (!$exists) {
                    $exists = $this->existsTransactionId($em, $transaction->getRelatedTransaction());
                }
            } while ($exists);

            $this->addTransaction($em, $transaction);
            $this->addTransaction($em, $transaction->getRelatedTransaction());

            if (is_callable($fn)) {
                $fn($em, $transaction);
            }

            return $transaction;
        });
    }

    public function process(Transaction $transaction, callable $fn = null): void
    {
        $this->em->transactional(function (EntityManager $em) use ($transaction, $fn) {
            $t1 = $transaction;
            $t2 = null;
            if ($t1 instanceof TransferTransaction) {
                $t2 = $t1->getRelatedTransaction();
            }

            if ($t1->getStatus()->equals(TransactionStatus::CREATED())) {
                if ($t2 instanceof TransferTransaction) {
                    if ($t2->isCredit()) {
                        // from t1 to t2
                        $origin = $t1->getAccount();
                        $destiny = $t2->getAccount();
                    } else {
                        // from t2 to t1
                        $origin = $t2->getAccount();
                        $destiny = $t1->getAccount();
                    }

                    // lock order to prevent deadlock
                    if ($origin->getCreatedAt() < $destiny->getCreatedAt()) {
                        $firstToLock = $origin;
                        $secondToLock = $destiny;
                    } else {
                        $firstToLock = $destiny;
                        $secondToLock = $origin;
                    }

                    $em->lock($firstToLock, LockMode::PESSIMISTIC_WRITE);
                    $em->lock($secondToLock, LockMode::PESSIMISTIC_WRITE);
                } else {
                    $em->lock($t1->getAccount(), LockMode::PESSIMISTIC_WRITE);
                }

                $this->updateBalance($em, $t1);
                if ($t2) {
                    $this->updateBalance($em, $t2);
                }

                if (is_callable($fn)) {
                    $fn($em, $transaction);
                }
            }
        });
    }

    public function cancel(Transaction $transaction, callable $fn = null): void
    {
        $this->em->transactional(function (EntityManager $em) use ($transaction, $fn) {
            if ($transaction->getStatus()->equals(TransactionStatus::CREATED())) {
                $transaction
                    ->setCancelledAt(new DateTime())
                    ->setStatus(TransactionStatus::CANCELLED());

                if ($transaction instanceof TransferTransaction) {
                    $related = $transaction->getRelatedTransaction();
                    $related
                        ->setCancelledAt(new DateTime())
                        ->setStatus(TransactionStatus::CANCELLED());

                    $em->persist($related);
                }

                $em->persist($transaction);

                if (is_callable($fn)) {
                    $fn($em, $transaction);
                }
            }
        });
    }

    private function updateBalance(EntityManagerInterface $em, Transaction $transaction)
    {
        $account = $transaction->getAccount();
        if ($transaction->isCredit()) {
            $account->setBalance($account->getBalance() + $transaction->getAmount());
        } else {
            $account->setBalance($account->getBalance() - $transaction->getAmount());
            if ($account->getBalance() < 0) {
                throw new InsufficientBalanceException();
            }
        }

        $transaction
            ->setProcessedAt(new DateTime())
            ->setStatus(TransactionStatus::PROCESSED());

        $em->persist($transaction);
        $em->persist($account);
    }

    private function existsTransactionId(EntityManagerInterface $em, Transaction $transaction)
    {
        /** @var TransactionRepository $repo */
        $repo = $em->getRepository(Transaction::class);
        $count = (int) $repo->count([
            'transactionId' => $transaction->getTransactionId(),
        ]);
        $exists = $count > 0;

        return $exists;
    }

    private function addTransaction(EntityManagerInterface $em, Transaction $transaction)
    {
        $transaction->setSignature($transaction->sign());
        $account = $transaction->getAccount();
        $account->setLastTransaction($transaction);

        $em->persist($transaction);
        $em->persist($account);
    }

    private function createWithdrawalTransaction(
        User $user,
        Account $account,
        float $amount,
        string $note
    ): WithdrawalTransaction {
        $previous = $account->getLastTransaction();
        $transaction = new WithdrawalTransaction($user, $account, $amount, $previous);
        $transactionId = $this->generateTransactionId($user, $account, $transaction);
        $transaction
            ->setNote($note)
            ->setTransactionId($transactionId);

        return $transaction;
    }

    private function createDepositTransaction(
        User $user,
        Account $account,
        float $amount,
        string $note
    ): DepositTransaction {
        $previous = $account->getLastTransaction();

        $transaction = new DepositTransaction($user, $account, $amount, $previous);
        $transactionId = $this->generateTransactionId($user, $account, $transaction);
        $transaction
            ->setNote($note)
            ->setTransactionId($transactionId);

        return $transaction;
    }

    private function createTransferTransaction(
        User $user,
        Account $origin,
        Account $destiny,
        float $amount,
        string $note
    ): TransferTransaction {
        $previous = $origin->getLastTransaction();
        $origTransaction = new TransferTransaction($user, $origin, $origin, $destiny, $amount, $previous);

        $previous = $destiny->getLastTransaction();
        $destTransaction = new TransferTransaction($user, $destiny, $origin, $destiny, $amount, $previous);

        $transactionId = $this->generateTransactionId($user, $origin, $origTransaction);

        $origTransaction
            ->setRelatedTransaction($destTransaction)
            ->setNote($note)
            ->setTransactionId($transactionId);

        $destTransaction
            ->setRelatedTransaction($origTransaction)
            ->setNote($note)
            ->setTransactionId($transactionId);

        return $origTransaction;
    }

    private function generateTransactionId(User $user, Account $account, Transaction $transaction): string
    {
        $salt = sprintf('%s:%s:%s', $user->getId(), $account->getId(), rand());
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $hashids = new Hashids($salt, 5, $alphabet);
        $id = $hashids->encode($transaction->getCreatedAt()->getTimestamp());

        if (strlen($id) > self::MAX_TRANSACTION_ID_LENGTH) {
            $id = substr($id, 0, self::MAX_TRANSACTION_ID_LENGTH);
        }

        return $id;
    }
}
