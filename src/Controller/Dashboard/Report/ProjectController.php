<?php

namespace App\Controller\Dashboard\Report;

use App\Entity\Helper\Report\CompanyResultsDTO;
use App\Entity\Resource\Project;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/report/project",
 *  name="dashboard_report_project_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ProjectController extends AbstractController
{
    /**
     * @Route(
     *  "/{projectId}",
     *  name="index",
     *  requirements={ "projectId": "^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$"}
     * )
     * @ParamConverter("project", options={"id": "projectId"})
     */
    public function index(Project $project)
    {
        $em = $this->getDoctrine()->getManager();
        $investors = [];
        $companyResults = new CompanyResultsDTO();

        return $this->render('dashboard/report/project/index.html.twig', [
            'user' => $this->getUser(),
            'now' => new DateTime(),
            'project' => $project,
            'investors' => $investors,
            'companyResults' => $companyResults,
            'contractorTransaction' => null,
            'engineerTransaction' => null,
            'brokerTransaction' => null,
            'agentTransaction' => null,
        ]);
    }
}
