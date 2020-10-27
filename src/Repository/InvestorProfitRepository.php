<?php

namespace App\Repository;

use App\Entity\Company\Company;
use App\Entity\InvestorProfit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvestorProfit|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvestorProfit|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvestorProfit[]    findAll()
 * @method InvestorProfit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvestorProfitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvestorProfit::class);
    }

    /**
     * @return InvestorProfit
     */
    public function findByInvestmentValue($investment, Company $company)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.company = :company')
            ->andWhere(':amount >= e.minAmount and :amount <= e.maxAmount')
            ->orWhere(':amount >= e.minAmount and e.maxAmount is null')

            ->setParameters([
                'amount' => $investment,
                'company' => $company,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
