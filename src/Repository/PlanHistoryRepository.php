<?php

namespace App\Repository;

use App\Entity\PlanHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanHistory[]    findAll()
 * @method PlanHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanHistory::class);
    }

    // /**
    //  * @return PlanHistory[] Returns an array of PlanHistory objects
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
    public function findOneBySomeField($value): ?PlanHistory
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
