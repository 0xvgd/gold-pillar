<?php

namespace App\Controller\Dashboard\Configuration;

use App\Controller\Traits\DatatableTrait;
use App\Entity\AssetEntryFee;
use App\Entity\InvestorProfit;
use App\Entity\Security\Application;
use App\Form\Dashboard\AssetEntryFeeType;
use App\Service\AssetEntryFeeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/investor/asset-entry-fee",
 *  name="dashboard_config_investor_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class AssetEntryFeeController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route(methods={"GET","POST"}, name="assetentryfee_create")
     */
    public function create(Request $request, AssetEntryFeeService $service): Response
    {
        $em = $this->getDoctrine()->getManager();
        $assetEntryFee = new AssetEntryFee();

        return $this->edit($request, $assetEntryFee, $service);
    }

    /**
     * @Route(
     *  "/{asset_entry_fee_id}",
     *  name="assetentryfee_edit",
     *  requirements={ "asset_entry_fee_id": "^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$" },
     *  methods={"GET","POST"}
     * )
     * @ParamConverter("application", options={"id": "application_id"})
     * @ParamConverter("assetEntryFee", options={"mapping": {"asset_entry_fee_id": "id"}})
     */
    public function edit(
        Request $request,
        AssetEntryFee $assetEntryFee,
        AssetEntryFeeService $service
    ): Response {
        $appName = $this->getParameter('app.name');
        $em = $this->getDoctrine()->getManager();

        $application = $em->getRepository(Application::class)->findOneBy([
            'name' => $appName,
        ]);

        $form = $this
            ->createForm(AssetEntryFeeType::class, $assetEntryFee)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($assetEntryFee);

                $this->addFlash('success', 'Item saved successfully.');

                return $this->redirectToRoute('application_config_application_index', [
                    'tab' => 'tab-asset-entry-fee',
                ]);
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->render('dashboard/configuration/asset-entry-fee/form.html.twig', [
            'form' => $form->createView(),
            'application' => $application,
        ]);
    }

    /**
     * @Route(
     *  "/{investor_profit_id}/remove",
     *  name="profits_remove",
     *  requirements={ "investor_profit_id": "^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$" },
     *  methods={"POST"}
     * )
     * @ParamConverter(
     *  "investorProfit",
     *  options={
     *      "mapping":
     *          {
     *              "application_id": "application",
     *              "investor_profit_id": "id"
     *          }
     * })
     */
    public function remove(
        InvestorProfit $investorProfit
    ): Response {
        try {
            $this->addFlash('success', 'TODO');

            return $this->redirectToRoute('application_config_application_index', [
                'tab' => 'tab-investor-profits',
            ]);
        } catch (\Exception $ex) {
            $this->addFlash('error', $ex->getMessage());

            return $this->redirectToRoute('application_config_application_index', [
                'tab' => 'tab-investor-profits',
            ]);
        }
    }
}
