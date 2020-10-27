<?php

namespace App\Service\Finance;

use App\Entity\Company\Company;
use App\Entity\Finance\Account;
use App\Entity\Finance\AccountSeq;
use App\Entity\Resource\Resource;
use App\Entity\Security\User;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class AccountService
{
    private const COMPANY = 'C';
    private const RESOURCE = 'R';
    private const USER = 'U';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createResourceAccount(Resource $resource, callable $fn = null): Account
    {
        return $this->createAccount($resource->getCompany(), self::RESOURCE, $fn);
    }

    public function createUserAccount(User $user, callable $fn = null): Account
    {
        return $this->createAccount($user->getCompany(), self::USER, $fn);
    }

    public function createCompanyAccount(Company $company, callable $fn = null): Account
    {
        return $this->createAccount($company, self::COMPANY, $fn);
    }

    private function createAccount(?Company $company, string $prefix, callable $fn = null): Account
    {
        return $this->em->transactional(function (EntityManager $em) use ($company, $prefix, $fn) {
            $seq = $this->getSequence($em, $company);

            $account = new Account($prefix);

            if (self::COMPANY === $prefix) {
                // company account is number 1
                $account->setNumber(1);
            } else {
                $account->setNumber($seq->getNextValue());
            }

            $em->persist($seq);
            $em->persist($account);

            if ($fn) {
                $fn($em, $account);
            }

            return $account;
        });
    }

    /**
     * TODO turn company parameter required!
     */
    private function getSequence(EntityManagerInterface $em, ?Company $company): AccountSeq
    {
        $seq = $em
            ->createQueryBuilder()
            ->select('s')
            ->from(AccountSeq::class, 's')
            ->setMaxResults(1)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_READ)
            ->getOneOrNullResult();

        if (!$seq instanceof AccountSeq) {
            $seq = new AccountSeq($company);
        }

        return $seq;
    }
}
