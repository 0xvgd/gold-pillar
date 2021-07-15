<?php

namespace App\Controller\Payment;

use App\Controller\Payment\Traits\PaymentTrait;
use App\Utils\TakePayments\Helpers\PayzoneHelper;
use App\Utils\TakePayments\PayzoneGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class ResultController extends AbstractController
{
    use PaymentTrait;

    private $payzoneGateway;
    private $payzoneHelper;

    public function __construct()
    {
        $this->payzoneHelper = new PayzoneHelper();
    }

    /**
     * @Route("/payment/default", name="payment_result1")
     */
    public function default(PayzoneGateway $payzoneGateway, $validate)
    {
        return $this->render('payment/results/result1.html.twig', [
            'validate' => $validate,
            'payzoneGateway' => $payzoneGateway,
        ]);
    }

    /**
     * @Route("/payment/result/error", name="payment_result1")
     */
    public function error(PayzoneGateway $payzoneGateway, $validate)
    {
        return $this->render('payment/results/error.html.twig', [
            'validate' => $validate,
            'payzoneGateway' => $payzoneGateway,
        ]);
    }
}
