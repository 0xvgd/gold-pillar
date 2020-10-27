<?php

namespace App\Controller\Frontend;

use App\Entity\Resource\Accommodation;
use App\Entity\Resource\Asset;
use App\Entity\Resource\Project;
use App\Entity\Resource\Property;
use App\Entity\Resource\Resource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/resources", name="app_resources_")
 */
class ResourcesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/{id}", name="frontend_resources_view")
     */
    public function view(Resource $resource)
    {
        if ($resource instanceof Property) {
            return $this->redirectToRoute('app_sales_view', [
                'slug' => $resource->getSlug(),
            ]);
        }

        if ($resource instanceof Asset) {
            return $this->redirectToRoute('app_assets_view', [
                'slug' => $resource->getSlug(),
            ]);
        }

        if ($resource instanceof Project) {
            return $this->redirectToRoute('app_projects_view', [
                'slug' => $resource->getSlug(),
            ]);
        }

        if ($resource instanceof Accommodation) {
            return $this->redirectToRoute('app_renting_view', [
                'slug' => $resource->getSlug(),
            ]);
        }

        return $this->redirectToRoute('frontend_index');
    }
}
