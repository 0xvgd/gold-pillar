<?php

namespace App\Controller\Dashboard\Company;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    /**
     * @Route("/dashboard/application/transfer", name="dashboard_application_transfer")
     */
    public function index()
    {
        return $this->render('dashboard/application/transfer/index.html.twig', [
        ]);
    }
}
