<?php

namespace App\Repository;

use App\Entity\ExecutiveClub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExecutiveClub|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExecutiveClub|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExecutiveClub[]    findAll()
 * @method ExecutiveClub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExecutiveClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExecutiveClub::class);
    }

    /**
     * @return ExecutiveClub
     */
    public function findByNumberOfSales($numberOfSales)
    {
        return $this->createQueryBuilder('e')
            ->andWhere(':numberOfSales >= e.minSales')
            ->andWhere(':numberOfSales <= e.maxSales')
            ->setParameter('numberOfSales', $numberOfSales)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
