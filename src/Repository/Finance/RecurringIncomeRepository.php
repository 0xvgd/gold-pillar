<?php

namespace App\Repository\Finance;

use App\Entity\Finance\RecurringIncome;
use App\Entity\Resource\Resource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecurringIncome|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecurringIncome|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecurringIncome[]    findAll()
 * @method RecurringIncome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecurringIncomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecurringIncome::class);
    }

    /**
     * @return float
     */
    public function getAmountByPeriodResource(Resource $resource, string $period)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.resource = :resource')
            ->andWhere('r.interval = :period')
            ->setParameters([
                'resource' => $resource,
                'period' => $period,
            ])
            ->select('SUM(r.amount) as balance')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
