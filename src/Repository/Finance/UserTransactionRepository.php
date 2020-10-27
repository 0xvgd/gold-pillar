<?php

namespace App\Repository\Finance;

use App\Entity\Finance\UserTransaction;
use App\Entity\Helper\TableValue;
use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTransaction[]    findAll()
 * @method UserTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTransaction::class);
    }

    /**
     * @return float Returns the balance
     */
    public function getBalance(User $user)
    {
        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->select('SUM(t.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getTotalProfitByProduct(User $user, string $globalTransactionId, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['investorProfit'] = TableValue::PROJECT_TRANSACTION_TYPE_INVESTOR_PROFIT;
        $params['globalTransactionId'] = $globalTransactionId;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :investorProfit')
            ->andWhere('t.globalTransactionId = :globalTransactionId')
            ->setParameters($params)
            ->select('SUM(t.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getProfitsByProduct(string $class, int $productId)
    {
        $params = [];
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['investorProfit'] = TableValue::PROJECT_TRANSACTION_TYPE_INVESTOR_PROFIT;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :investorProfit')
            ->setParameters($params)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return float Returns the cost
     */
    public function getCostByProduct(User $user, string $globalTransactionId, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['globalTransactionId'] = $globalTransactionId;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.globalTransactionId = :globalTransactionId')
            ->andWhere('t.amount < 0')
            ->setParameters($params)
            ->select('SUM(t.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getBalanceByProduct(User $user, string $globalTransactionId, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['investmentReturn'] = TableValue::PROJECT_TRANSACTION_TYPE_INVESTMENT_RETURN;
        $params['globalTransactionId'] = $globalTransactionId;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type != :investmentReturn')
            ->andWhere('t.globalTransactionId = :globalTransactionId')
            ->setParameters($params)
            ->select('SUM(t.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getContractorBalanceByProduct(User $user, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['contractorEarnings'] = TableValue::PROJECT_TRANSACTION_TYPE_CONTRACTOR_EARNINGS;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :contractorEarnings')
            ->setParameters($params)
            ->select('SUM(t.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getContractorTransactionByProduct(User $user, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['contractorEarnings'] = TableValue::PROJECT_TRANSACTION_TYPE_CONTRACTOR_EARNINGS;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :contractorEarnings')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getEngineerTransactionByProduct(User $user, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['engineerEarnings'] = TableValue::PROJECT_TRANSACTION_TYPE_ENGINEERS_EARNINGS;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :engineerEarnings')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getBrokerTransactionByProduct(User $user, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['brokerEarnings'] = TableValue::PROJECT_TRANSACTION_TYPE_BROKER_EARNINGS;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :brokerEarnings')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getPropertyComissionTransactionByProduct(User $user, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['agentComission'] = TableValue::TRANSACTION_TYPE_AGENT_COMISSION;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :agentComission')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getAgentTransactionByProduct(User $user, string $class, int $productId)
    {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['agentEarnings'] = TableValue::PROJECT_TRANSACTION_TYPE_AGENT_EARNINGS;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :agentEarnings')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getTransactionByProduct(
        User $user,
        string $class,
        int $productId,
        int $transactionType
    ) {
        $params = [];
        $params['user'] = $user;
        $params['class'] = $class;
        $params['productId'] = $productId;
        $params['transactionType'] = $transactionType;

        return $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->andWhere('a.user = :user')
            ->andWhere('t.transactionObjectId = :productId')
            ->andWhere('t.transactionObjectClass = :class')
            ->andWhere('t.type = :transactionType')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
