<?php

namespace App\Repository\Negotiation;

use App\Entity\Negotiation\Offer;
use App\Entity\Person\Tenant;
use App\Entity\Resource\Accommodation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    public function getOfferAcceptedByResource(Accommodation $accommodation)
    {
        return $this->createQueryBuilder('o')
            ->join('o.resource', 'r')
            ->join(Accommodation::class, 'a', 'WITH', 'o.resource = a')
            ->andWhere('a = :accommodation')
            ->andWhere('o.offerAcceptedAt is not null or o.counterOfferAcceptedAt is not null')
            ->andWhere('o.removedAt is null')
            ->setParameter('accommodation', $accommodation)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getOfferAcceptedByResourceTenant(Accommodation $accommodation, Tenant $tenant)
    {
        return $this->createQueryBuilder('o')
            ->join('o.resource', 'r')
            ->join('o.tenant', 't')
            ->join(Accommodation::class, 'a', 'WITH', 'o.resource = a')
            ->andWhere('a = :accommodation')
            ->andWhere('t = :tenant')
            ->andWhere('o.offerAcceptedAt is not null or o.counterOfferAcceptedAt is not null')
            ->andWhere('o.removedAt is null')
            ->setParameters([
                'accommodation' => $accommodation,
                'tenant' => $tenant,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
