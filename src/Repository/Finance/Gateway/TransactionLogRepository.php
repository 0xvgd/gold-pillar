<?php

namespace App\Repository\Finance\Gateway;

use App\Entity\Finance\Gateway\TransactionLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransactionLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionLog[]    findAll()
 * @method TransactionLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionLog::class);
    }

    // /**
    //  * @return TransactionLog[] Returns an array of TransactionLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TransactionLog
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
