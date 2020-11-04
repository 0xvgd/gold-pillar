<?php

namespace App\Controller\Payment;

use App\Controller\Payment\Traits\PaymentTrait;
use App\Utils\TakePayments\Gateway\Constants\IntegrationType;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Helpers\PaymentData;
use App\Utils\TakePayments\Helpers\PayzoneHelper;
use App\Utils\TakePayments\PayzoneGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProcessController extends AbstractController
{
    use PaymentTrait;

    private $payzoneGateway;
    private $payzoneHelper;

    public function __construct()
    {
        $this->payzoneHelper = new PayzoneHelper();
    }

    /**
     * @Route("/payment/process", name="payment_process")
     */
    public function index(
        Request $request,
        string $certDir,
        UrlHelper $urlHelper,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $retorno = null;
        $logger->info('Processando...');
        $baseUrl = $this->generateUrl(
            'app_home',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $paymentData = new PaymentData();
        $paymentResponse = null;
        $action = 'pzgact';
        if ($request->query->has('pzgact')) {
            $action = $request->query->get('pzgact');
        } elseif ($request->request->has('PaRes')) {
            $action = 'threedsecure';
            $paymentData->setPaRes($request->request->get('PaRes'));
            $paymentData->setMd($request->request->get('MD'));
        }

        $config = $this->getConfiguration();
        $this->payzoneGateway = new PayzoneGateway($paymentData, $em, $config);

        $IntegrationType = $this->payzoneGateway->getIntegrationType();
        $SecretKey = $this->payzoneGateway->getSecretKey();
        $HashMethod = $this->payzoneGateway->getHashMethod();

        $rgeplRequestGatewayEntryPointList = new RequestGatewayEntryPointList();
        $logger->info('Verifcando tipo de integração...');
        if (IntegrationType::DIRECT == $IntegrationType) {
            $logger->info($action);
            switch ($action) {
                case 'threedsecure':
                    $paymentResponse = $this->threadProcess(
                        $this->payzoneGateway,
                        $paymentData,
                        $certDir
                    );

                    $jsonData = json_encode($paymentResponse);
                    $logger->info($jsonData);
                    $retorno = <<<EOF
    <script>
      window.parent.postMessage({'option':'iframesrc','value':'three-response'}, '$baseUrl');
      window.parent.postMessage({'option':'threedresponse','value':' $jsonData'},'$baseUrl');
    </script>          
EOF;

                    break;
            }
        }
        $logger->info('enviando retorno');
        $logger->info($retorno);

        return new Response($retorno);
    }
}
