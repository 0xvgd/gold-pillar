<?php

namespace App\Repository\Transactions;

use App\Entity\Helper\TableValue;
use App\Entity\Security\User;
use App\Entity\Transactions\PropertySaleTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PropertySaleTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertySaleTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertySaleTransaction[]    findAll()
 * @method PropertySaleTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertySaleTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertySaleTransaction::class);
    }

    /**
     * @return PropertySaleTransaction[] Returns an array of SaleTransaction objects
     */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getBalanceByUser(User $user)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->select('SUM(t.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float Returns the balance
     */
    public function getReferredAgentBonusBalanceByUser(User $user)
    {
        $params = [
            'user' => $user,
            'type' => TableValue::TRANSACTION_TYPE_PARENT_AGENT_BONUS,
        ];

        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->andWhere('t.type = :type')
            ->setParameters($params)
            ->select('SUM(t.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
