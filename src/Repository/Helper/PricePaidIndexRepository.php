<?php

namespace App\Repository\Helper;

use App\Entity\Helper\PricePaidIndex;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PricePaidIndex|null find($id, $lockMode = null, $lockVersion = null)
 * @method PricePaidIndex|null findOneBy(array $criteria, array $orderBy = null)
 * @method PricePaidIndex[]    findAll()
 * @method PricePaidIndex[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricePaidIndexRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PricePaidIndex::class);
    }

    // /**
    //  * @return PricePaidIndex[] Returns an array of PricePaidIndex objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PricePaidIndex
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
