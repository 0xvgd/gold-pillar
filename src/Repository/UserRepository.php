<?php

namespace App\Repository;

use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByName(string $name = null)
    {
        $name = strtolower($name);
        $params['arg'] = "%{$name}%";

        $qb = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->andWhere('LOWER(u.name) LIKE :arg')
            ->setParameters($params);

        return $qb->getQuery()
            ->setMaxResults(10)
            ->getResult();
    }

    /**
     * @param string    $hash
     * @param bool|null $ativo
     *
     * @return User|null
     */
    public function getByUserHash($hash, $active = true)
    {
        $params = [
            'hash' => $hash,
        ];

        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.userHash = :hash');

        return $qb
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
