<?php

namespace App\Repository\Finance;

use App\Entity\Finance\AccountSeq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccountSeq|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountSeq|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountSeq[]    findAll()
 * @method AccountSeq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountSeqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountSeq::class);
    }

    // /**
    //  * @return AccountSeq[] Returns an array of AccountSeq objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AccountSeq
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
