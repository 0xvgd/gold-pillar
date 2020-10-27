<?php

namespace App\Controller\Dashboard\Configuration;

use App\Entity\AssetInvestmentClass;
use App\Entity\Security\Application;
use App\Form\Dashboard\AssetInvestmentClassType;
use App\Service\AssetInvestmentClassService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/application/asset-investiment-class",
 *  name="dashboard_config_app_asset_investment_class_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AssetInvestmentClassController extends AbstractController
{
    /**
     * @Route(methods={"GET","POST"}, name="create")
     */
    public function create(Request $request, AssetInvestmentClassService $service): Response
    {
        $appName = $this->getParameter('app.name');
        $em = $this->getDoctrine()->getManager();

        $application = $em->getRepository(Application::class)->findOneBy([
            'name' => $appName,
        ]);

        $assetInvestmentClass = new AssetInvestmentClass();

        $assetInvestmentClass->setApplicationSettings($application->getApplicationSettings());

        return $this->edit($request, $assetInvestmentClass, $service);
    }

    /**
     * @Route(
     *  "/{investment_class_id}",
     *  name="edit",
     *  requirements={ "investment_class_id": "^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$" },
     *  methods={"GET","POST"}
     * )
     * @ParamConverter("application", options={"id": "application_id"})
     * @ParamConverter("assetInvestmentClass", options={"mapping": {"investment_class_id": "id"}})
     */
    public function edit(
        Request $request,
        AssetInvestmentClass $assetInvestmentClass,
        AssetInvestmentClassService $service
    ): Response {
        //$this->denyAccessUnlessGranted(null, $application);
        // $this->denyAccessUnlessGranted(null, $plan);

        $appName = $this->getParameter('app.name');
        $em = $this->getDoctrine()->getManager();

        $application = $em->getRepository(Application::class)->findOneBy([
            'name' => $appName,
        ]);

        $form = $this
            ->createForm(AssetInvestmentClassType::class, $assetInvestmentClass)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($assetInvestmentClass);

                $this->addFlash('success', 'Investment class saved successfully.');

                return $this->redirectToRoute('application_config_application_index', [
                    'tab' => 'tab-executive-club',
                ]);
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->render('dashboard/configuration/investment_class/form.html.twig', [
            'form' => $form->createView(),
            'application' => $application,
        ]);
    }

    /**
     * @Route(
     *  "/{investment_class_id}/remove",
     *  name="remove",
     *  requirements={ "investment_class_id": "^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$" },
     *  methods={"POST"}
     * )
     * @ParamConverter(
     * "executiveClub",
     *  options={"mapping": {"application_id": "application", "investment_class_id": "id"}}
     * )
     */
    public function remove(
        AssetInvestmentClass $assetInvestmentClass
    ): Response {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($assetInvestmentClass);
            $em->flush();

            $this->addFlash('success', 'Item deleted successfully.');

            return $this->redirectToRoute('application_config_application_index', [
                'tab' => 'tab-executive-club',
            ]);
        } catch (\Exception $ex) {
            $this->addFlash('error', $ex->getMessage());

            return $this->redirectToRoute('application_config_application_index', [
                'tab' => 'tab-executive-club',
            ]);
        }
    }
}
