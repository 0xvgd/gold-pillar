<?php

namespace App\Service\Company;

use App\Entity\Company\Company;
use App\Service\Finance\AccountService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * CompanyService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class CompanyService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AccountService
     */
    private $accountService;

    public function __construct(
        EntityManagerInterface $em,
        AccountService $accountService
    ) {
        $this->em = $em;
        $this->accountService = $accountService;
    }

    /**
     * @throws Exception
     */
    public function save(Company $company)
    {
        $this->em->beginTransaction();

        try {
            if (!$company->getId()) {
                $company
                    ->setCreatedAt(new DateTime());

                $companyAccount = $this
                    ->accountService
                    ->createCompanyAccount($company);
                $company->setAccount($companyAccount);
            }

            if ($company->getDefaultCompany()) {
                $this->updateDefaultCompanyFlag($company);
            }

            $this->em->persist($company);
            $this->em->flush();
            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    public function updateDefaultCompanyFlag(Company $company)
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('UPDATE companies SET companies.default_company = false
                WHERE companies.id  != :company_id');
        $stmt->bindValue(':company_id', $company->getId());

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getDefaultCompany()
    {
        $company = $this->em->getRepository(Company::class)->findOneBy([
            'defaultCompany' => true,
        ]);

        return $company;
    }
}
