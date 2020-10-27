<?php

namespace App\Repository;

use App\Entity\AssetInvestmentClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssetInvestmentClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetInvestmentClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetInvestmentClass[]    findAll()
 * @method AssetInvestmentClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetInvestmentClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetInvestmentClass::class);
    }

    /**
     * @return AssetInvestmentClass
     */
    public function findByInvestment($investment)
    {
        return $this->createQueryBuilder('e')
            ->andWhere(':amount >= e.minAmount and :amount <= e.maxAmount')
            ->orWhere(':amount >= e.minAmount and e.maxAmount is null')
            ->setParameter('amount', $investment)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
