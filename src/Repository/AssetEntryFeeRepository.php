<?php

namespace App\Repository;

use App\Entity\AssetEntryFee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssetEntryFee|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetEntryFee|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetEntryFee[]    findAll()
 * @method AssetEntryFee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetEntryFeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetEntryFee::class);
    }

    /**
     * @return AssetEntryFee
     */
    public function findByInvesmentAmount($investment)
    {
        return $this->createQueryBuilder('e')
            ->andWhere(':investment >= e.min')
            ->andWhere(':investment <= e.max')
            ->setParameter('investment', $investment)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
