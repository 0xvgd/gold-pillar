<?php

namespace App\Repository\Renting;

use App\Entity\Resource\Accommodation;
use App\Enum\PropertyStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Accommodation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accommodation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accommodation[]    findAll()
 * @method Accommodation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccommodationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accommodation::class);
    }

    public function getTotalSalesByUser($user)
    {
        $params = [
            'user' => $user,
            'propertyStatus' => PropertyStatus::SOLD(),
        ];

        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.propertyStatus = :propertyStatus')
            ->andWhere('p.user = :user')
            ->setParameters($params)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalSalesByMonth($user, $month)
    {
        $params = [
            'user' => $user,
            'propertyStatus' => PropertyStatus::SOLD(),
            'month' => $month,
        ];

        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.propertyStatus = :propertyStatus')
            ->andWhere('p.user = :user')
            ->andWhere('MONTH(p.soldAt) = :month')
            ->setParameters($params)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getWithSameSlug($slug, $id): ?Accommodation
    {
        $params = [
            'slug' => $slug,
            'id' => $id,
        ];

        return $this->createQueryBuilder('p')
            ->andWhere('p.slug = :slug')
            ->andWhere('p.id != :id')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
