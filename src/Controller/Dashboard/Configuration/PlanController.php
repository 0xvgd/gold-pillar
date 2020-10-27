<?php

namespace App\Controller\Dashboard\Configuration;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Company\Company;
use App\Entity\Plan;
use App\Entity\Security\Application;
use App\Form\Dashboard\PlanType;
use App\Service\PlanService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/companies",
 *  name="dashboard_companies_plan_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class PlanController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route(
     * "/{company_id}/plan",
     * name="create",
     * requirements={ "company_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
     * methods={"GET","POST"},
     * )
     * @ParamConverter("company", options={"mapping": {"company_id": "id"}})
     */
    public function create(
        Request $request,
        PlanService $service,
        Company $company
    ): Response {
        $plan = new Plan();
        $plan->setCompay($company);

        return $this->edit($request, $plan, $service);
    }

    /**
     * @Route(
     *  "/plan/{plan_id}",
     *  name="edit",
     *  requirements={ "plan_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
     *  methods={"GET","POST"}
     * )
     * @ParamConverter("plan", options={"mapping": {"plan_id": "id"}})
     */
    public function edit(
        Request $request,
        Plan $plan,
        PlanService $service
    ): Response {
        $form = $this
            ->createForm(PlanType::class, $plan)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($plan);

                $this->addFlash('success', 'Plan saved successfully.');

                return $this->redirectToRoute('dashboard_config_companies_edit', [
                    'id' => $plan->getCompany()->getId(),
                    'tab' => 'tab-plans',
                ]);
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->render('dashboard/configuration/plan/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $plan,
        ]);
    }

    /**
     * @Route(
     *  "/{plan_id}/remove",
     *  name="remove",
     *  requirements={ "plan_id": "^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$" },
     *  methods={"POST"}
     * )
     * @ParamConverter("plan", options={"mapping": {"application_id": "application", "plan_id": "id"}})
     */
    public function remove(
        Plan $plan
    ): Response {
        try {
            $this->addFlash('success', 'TODO');
        } catch (\Exception $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirectToRoute('dashboard_config_companies_edit', [
            'id' => $plan->getCompany()->getId(),
            'tab' => 'tab-plans',
        ]);
    }
}
