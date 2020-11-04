<?php

namespace App\Service;

use App\Entity\Resource\Resource;
use App\Enum\PostStatus;
use App\Service\Company\CompanyService;
use App\Service\Finance\AccountService;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class ResourceService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var CompanyService
     */
    private $companyService;

    public function __construct(
        EntityManagerInterface $em,
        AccountService $accountService,
        CompanyService $companyService
    ) {
        $this->em = $em;
        $this->accountService = $accountService;
        $this->companyService = $companyService;
    }

    /**
     * @param resource $property
     *
     * @throws Exception
     */
    public function save(Resource $resource)
    {
        if (!$resource->getAccount()) {
            $account = $this->accountService->createResourceAccount($resource);
            $resource->setAccount($account);
        }

        if (!$resource->getCompany()) {
            $company = $this->companyService->getDefaultCompany();
            $resource->setCompany($company);
        }

        $this->em->beginTransaction();
        try {
            if (!$resource->getId()) {
                $resource
                    ->setCreatedAt(new DateTime())
                    ->setSlug($this->generateSlug($resource))
                    ->setPostStatus(PostStatus::ON_APPROVAL());
            }

            $this->em->persist($resource);
            $this->em->flush();
            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    public function generateSlug(Resource $resource)
    {
        $slugify = new Slugify();
        $attempts = 0;
        $exists = false;
        $repository = $this->em->getRepository(get_class($resource));
        $query = $repository
            ->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->where('e.slug = :slug')
            ->andWhere('e.id != :id')
            ->getQuery();

        do {
            $slug = $slugify->slugify($resource->getName());
            if ($attempts > 0) {
                $slug .= "-$attempts";
            }
            $count = (int) $query
                ->setParameters([
                    'slug' => $slug,
                    'id' => (string) $resource->getId(),
                ])
                ->getSingleScalarResult();
            $exists = $count > 0;
            ++$attempts;
        } while ($exists);

        return $slug;
    }

    public function getEntityManager()
    {
        return $this->em;
    }
}
