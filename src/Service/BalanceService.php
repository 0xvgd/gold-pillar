<?php

namespace App\Service;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * BalanceService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class BalanceService
{
    /**
     * @param EntityManagerInterface $em
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getTransactionsByUser(User $user)
    {
        // TODO
        $transactions = [];

        return $transactions;
    }

    public function getBalanceByUser(User $user)
    {
        // TODO
        $balance = 0;

        return $balance;
    }

    public function getSalesTransactionsByUser(User $user)
    {
        // TODO
        $transactions = [];

        return $transactions;
    }

    public function getSalesBalanceByUser(User $user)
    {
        // TODO
        $balance = 0;

        return $balance;
    }

    public function getReferredAgentBonusBalanceByUser(User $user)
    {
        // TODO
        $balance = 0;

        return $balance;
    }

    public function doTransFer($accountFrom, $accountTo, $amount, $date)
    {
    }
}
