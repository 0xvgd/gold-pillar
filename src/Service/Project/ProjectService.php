<?php

namespace App\Service\Project;

use App\Entity\Person\Broker;
use App\Entity\Resource\Project;
use App\Entity\Resource\Resource;
use App\Enum\ProjectStatus;
use App\Service\Company\CompanyService;
use App\Service\Finance\AccountService;
use App\Service\ResourceService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * ProjectService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class ProjectService extends ResourceService
{
    /**
     * @var AuthorizationChecker
     */
    private $authChecker;

    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authChecker,
        AccountService $accountService,
        CompanyService $companyService
    ) {
        parent::__construct($em, $accountService, $companyService);
        $this->authChecker = $authChecker;
    }

    /**
     * @param Property $property
     *
     * @throws Exception
     */
    public function save(Resource $project)
    {
        if (!$project instanceof Project) {
            throw new Exception('The resource must be a instance of Project');
        }

        if (!$project->getId()) {
            $project->setProjectStatus(ProjectStatus::PENDING());
            $project->setReferenceCode($this->generateReferenceCode());
            $project->getTotalInvested()->setAmount(0);
            if ($this->authChecker->isGranted('ROLE_BROKER')) {
                $broker = new Broker();
                $broker->setUser($project->getOwner());
                $broker->setDescription('');
                $project->setBroker($broker);
            }
        }

        if (!$project->getReferenceCode()) {
            $project->setReferenceCode($this->generateReferenceCode());
        }

        parent::save($project);
    }

    public function getTransactions(Project $project)
    {
        return [];
    }

    public function getCompanyTransactions(Project $project)
    {
        return [];
    }

    public function generateReferenceCode()
    {
        $referenceCode = null;
        $codeExists = true;

        while ($codeExists) {
            $referenceCode = 'PROJ-'.substr(strtoupper(sha1(random_bytes(4))), 0, 8);
            $reserve = $this->getEntityManager()->getRepository(Project::class)->findOneBy(
                [
                    'referenceCode' => $referenceCode,
                ]
            );

            if (!$reserve) {
                $codeExists = false;
            }
        }

        return $referenceCode;
    }
}
