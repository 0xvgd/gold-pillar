<?php

namespace App\Repository\Sales;

use App\Entity\Resource\Property;
use App\Enum\PropertyStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function getTotalSalesByUser($user)
    {
        return $this
            ->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.propertyStatus = :propertyStatus')
            ->andWhere('p.owner = :user')
            ->setParameters([
                'user' => $user,
                'propertyStatus' => PropertyStatus::SOLD(),
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalSalesByMonth($user, $month)
    {
        return $this
            ->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.propertyStatus = :propertyStatus')
            ->andWhere('p.owner = :user')
            ->andWhere('MONTH(p.soldAt) = :month')
            ->setParameters([
                'user' => $user,
                'propertyStatus' => PropertyStatus::SOLD(),
                'month' => $month,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getWithSameSlug($slug, $id): ?Property
    {
        return $this
            ->createQueryBuilder('p')
            ->andWhere('p.slug = :slug')
            ->andWhere('p.id != :id')
            ->setParameters([
                'slug' => $slug,
                'id' => $id,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
