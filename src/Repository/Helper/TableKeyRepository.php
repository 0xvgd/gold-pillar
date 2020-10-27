<?php

namespace App\Repository\Helper;

use App\Entity\Helper\TableKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TableKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableKey[]    findAll()
 * @method TableKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableKey::class);
    }

    // /**
    //  * @return TableKey[] Returns an array of TableKey objects
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
    public function findOneBySomeField($value): ?TableKey
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
