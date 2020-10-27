<?php

namespace App\Controller\Dashboard\Sales;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SalesController extends AbstractController
{
    /**
     * @Route("/dashboard/sales", name="dashboard_sales")
     */
    public function index()
    {
        return $this->render('dashboard/sales/index.html.twig', [
        ]);
    }
}
