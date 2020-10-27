<?php

namespace App\Repository\Helper;

use App\Entity\Helper\TableValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TableValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableValue[]    findAll()
 * @method TableValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableValue::class);
    }

    // /**
    //  * @return TableValue[] Returns an array of TableValue objects
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
    public function findOneBySomeField($value): ?TableValue
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
