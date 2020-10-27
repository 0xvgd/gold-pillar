<?php

namespace App\Controller\Dashboard\Report;

use App\Entity\Resource\Property;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/report/property",
 *  name="dashboard_report_property_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class PropertyController extends AbstractController
{
    /**
     * @Route(
     *  "/{propertyId}",
     *  name="index",
     *  requirements={ "propertyId": "^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$"}
     * )
     * @ParamConverter("property", options={"id": "propertyId"})
     */
    public function index(Property $property)
    {
        $em = $this->getDoctrine()->getManager();
        $agent = $property->getAgent();
        $parentAgentTransaction = null;
        $grandParentAgentTransaction = null;

        return $this->render('dashboard/report/property/index.html.twig', [
            'user' => $this->getUser(),
            'now' => new DateTime(),
            'property' => $property,
            'companyTransaction' => null,
            'agentTransaction' => null,
            'parentAgentTransaction' => $parentAgentTransaction,
            'grandparentAgentTransaction' => $grandParentAgentTransaction,
        ]);
    }
}
