<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/acquisitions")
 */
class AcquisitionsController extends AbstractController
{
    /**
     * @Route("/", name="app_acquisitions_index")
     */
    public function index()
    {
        return $this->render('frontend/acquisitions/index.html.twig', [
        ]);
    }
}
