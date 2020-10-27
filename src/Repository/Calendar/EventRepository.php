<?php

namespace App\Repository\Calendar;

use App\Entity\Calendar\Event;
use App\Entity\Security\User;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[]
     */
    public function findByUser(
        User $user,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        int $maxResults = 100
    ): array {
        $qb = $this
            ->createQueryBuilder('e')
            ->join('e.participants', 'p')
            ->where('p = :user')
            ->andWhere('e.date >= :startDate')
            ->andWhere('e.date <= :endDate')
            ->setParameters([
                'user' => $user,
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
            ])
            ->orderBy('e.date', 'ASC')
            ->addOrderBy('e.startTime', 'ASC');
        if ($maxResults > 0) {
            $qb->setMaxResults($maxResults);
        }

        $rs = $qb
            ->getQuery()
            ->getResult()
        ;

        return $rs;
    }
}
