<?php

namespace App\Controller\Dashboard;

use App\Entity\Resource\Accommodation;
use App\Entity\Resource\Asset;
use App\Entity\Resource\Project;
use App\Entity\Resource\Property;
use App\Entity\Resource\Resource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *  "/{_locale}/dashboard/resources",
 *  name="dashboard_resources_",
 *  requirements={"_locale"="%app.supported_locales%"})
 */
class ResourcesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->redirectToRoute('dashboard_index');
    }

    /**
     * @Route("/{id}", name="view")
     */
    public function view(Resource $resource)
    {
        if ($resource instanceof Property) {
            return $this->redirectToRBoute('dashboard_sales_properties_edit', [
                'id' => $resource->getId(),
            ]);
        }

        if ($resource instanceof Asset) {
            return $this->redirectToRoute('dashboard_assets_assets_edit', [
                'id' => $resource->getId(),
            ]);
        }

        if ($resource instanceof Project) {
            return $this->redirectToRoute('dashboard_projects_projects_edit', [
                'id' => $resource->getId(),
            ]);
        }

        if ($resource instanceof Accommodation) {
            return $this->redirectToRoute('dashboard_renting_accommodations_edit', [
                'id' => $resource->getId(),
            ]);
        }

        return $this->redirectToRoute('dashboard_index');
    }
}
