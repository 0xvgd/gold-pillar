<?php

namespace App\Controller\Dashboard\Configuration;

use App\Entity\Company\Company;
use App\Entity\ExecutiveClub;
use App\Form\Dashboard\ExecutiveClubType;
use App\Service\ExecutiveClubService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/companies",
 *  name="dashboard_companies_executive_club_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ExecutiveClubController extends AbstractController
{
    /**
     * @Route(
     * "/{company_id}/executive-club",
     * name="create",
     * requirements={ "company_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
     * methods={"GET","POST"},
     * )
     * @ParamConverter("company", options={"mapping": {"company_id": "id"}})
     */
    public function create(
        Request $request,
        ExecutiveClubService $service,
        Company $company
    ): Response {
        $executiveClub = new ExecutiveClub();
        $executiveClub->setCompany($company);

        return $this->edit($request, $executiveClub, $service);
    }

    /**
     * @Route(
     *  "/executive-club/{club_id}",
     *  name="edit",
     *  requirements={ "club_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
     *  methods={"GET","POST"}
     * )
     * @ParamConverter("application", options={"id": "application_id"})
     * @ParamConverter("executiveClub", options={"mapping": {"club_id": "id"}})
     */
    public function edit(
        Request $request,
        ExecutiveClub $executiveClub,
        ExecutiveClubService $service
    ): Response {
        $form = $this
            ->createForm(ExecutiveClubType::class, $executiveClub)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($executiveClub);

                $this->addFlash('success', 'Executive club saved successfully.');

                return $this->redirectToRoute('dashboard_config_companies_edit', [
                    'id' => $executiveClub->getCompany()->getId(),
                    'tab' => 'tab-executive-club',
                ]);
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->render('dashboard/configuration/executive_club/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $executiveClub,
        ]);
    }

    /**
     * @Route(
     *  "/{club_id}/remove",
     *  name="remove",
     *  requirements={ "plan_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
     *  methods={"POST"}
     * )
     * @ParamConverter("executiveClub", options={"mapping": {"application_id": "application", "club_id": "id"}})
     */
    public function remove(
        ExecutiveClub $executiveClub
    ): Response {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($executiveClub);
            $em->flush();

            $this->addFlash('success', 'Item deleted successfully.');
        } catch (\Exception $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirectToRoute('dashboard_config_companies_edit', [
            'id' => $executiveClub->getCompany()->getId(),
            'tab' => 'tab-executive-club',
        ]);
    }
}
