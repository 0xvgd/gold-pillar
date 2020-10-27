<?php

namespace App\Repository\Asset;

use App\Entity\Asset\AssetEquity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssetEquity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetEquity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetEquity[]    findAll()
 * @method AssetEquity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetEquityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetEquity::class);
    }

    // /**
    //  * @return AssetEquity[] Returns an array of AssetEquity objects
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
    public function findOneBySomeField($value): ?AssetEquity
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
