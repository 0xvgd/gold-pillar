<?php

namespace App\Repository;

use App\Entity\Subscription;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * @return Subscription[] Returns an array of active Subscriptions
     */
    public function getAllActive()
    {
        $monthRef = date('n');
        $yearRef = date('Y');
        $today = date('y-m-d');
        $subscriptions = $this->createQueryBuilder('s')
            ->join('s.user', 'u')
            ->leftJoin(
                Transaction::class,
                't',
                'WITH',
                't.user = u and t.monthRef = :monthRef and t.yearRef = :yearRef'
            )
            ->where('t.id is null')
            ->andWhere('s.validTo >= :today')
            ->setParameter('monthRef', $monthRef)
            ->setParameter('yearRef', $yearRef)
            ->setParameter('today', $today)
            ->orderBy('s.subscribedAt', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $subscriptions;
    }
}
