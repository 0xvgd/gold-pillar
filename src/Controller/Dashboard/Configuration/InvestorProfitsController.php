<?php

namespace App\Controller\Dashboard\Configuration;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Company\Company;
use App\Entity\InvestorProfit;
use App\Form\Dashboard\InvestorProfitType;
use App\Service\InvestorProfitService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/config/companies",
 *  name="dashboard_companies_investor_profits_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class InvestorProfitsController extends AbstractController
{
    use DatatableTrait;

    /**
     * @Route(
     * "/{company_id}/profits",
     * name="create",
     * requirements={ "company_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
     * methods={"GET","POST"},
     * )
     * @ParamConverter("company", options={"mapping": {"company_id": "id"}})
     */
    public function create(
        Request $request,
        InvestorProfitService $service,
        Company $company
    ): Response {
        $investorProfit = new InvestorProfit();
        $investorProfit->setCompany($company);

        return $this->edit($request, $investorProfit, $service);
    }

    /**
     * @Route(
     *  "/profits/{investor_profit_id}",
     *  name="edit",
     *  requirements={ "investor_profit_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
     *  methods={"GET","POST"}
     * )
     * @ParamConverter("investorProfit", options={"mapping": {"investor_profit_id": "id"}})
     */
    public function edit(
        Request $request,
        InvestorProfit $investorProfit,
        InvestorProfitService $service
    ): Response {
        $form = $this
            ->createForm(InvestorProfitType::class, $investorProfit)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $service->save($investorProfit);

                $this->addFlash('success', 'Item saved successfully.');

                return $this->redirectToRoute('dashboard_config_companies_edit', [
                    'id' => $investorProfit->getCompany()->getId(),
                    'tab' => 'tab-investor-profits',
                ]);
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }

        return $this->render('dashboard/configuration/investor_profits/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $investorProfit,
        ]);
    }

    /**
     * @Route(
     *  "/{investor_profit_id}/remove",
     *  name="remove",
     *  requirements={ "investor_profit_id": "^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$" },
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
        InvestorProfit $investorProfit,
        EntityManagerInterface $em
    ): Response {
        try {
            $em->remove($investorProfit);
            $em->flush();

            $this->addFlash('success', 'Item deleted successfully.');
        } catch (\Exception $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirectToRoute('dashboard_config_companies_edit', [
            'id' => $investorProfit->getCompany()->getId(),
            'tab' => 'tab-investor-profits',
        ]);
    }
}
