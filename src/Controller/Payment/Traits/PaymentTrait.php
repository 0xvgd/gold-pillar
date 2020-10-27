<?php

namespace App\Controller\Payment\Traits;

use App\Controller\Payment\ResultController;
use App\Entity\Finance\EntryFee;
use App\Entity\Finance\Investment;
use App\Entity\Finance\Transaction;
use App\Entity\Reserve\Reserve;
use App\Enum\InvestmentStatus;
use App\Service\Finance\TransactionService;
use App\Utils\TakePayments\Gateway\Constants\IntegrationType;
use App\Utils\TakePayments\Gateway\Constants\PayzoneResponseOutcomes;
use App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages\CardDetailsTransaction;
use App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages\CrossReferenceTransaction;
use App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages\ThreeDSecureAuthentication;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Helpers\Configuration;
use App\Utils\TakePayments\Helpers\PaymentData;
use App\Utils\TakePayments\PayzoneGateway;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

trait PaymentTrait
{
    protected function process(
        Request $request,
        EntityManagerInterface $em,
        PaymentData $paymentData,
        string $successUrl,
        string $errorUrl,
        $certDir
    ) {
        $paymentData->setTransactionDateTime(date('Y-m-d H:i:s P'));
        $config = $this->getConfiguration();
        $this->payzoneGateway = new PayzoneGateway($paymentData, $em, $config);

        if ($request->query->has('pzgact')) {
            $action = $request->query->get('pzgact');
        } elseif ($request->query->has('PaRes')) {
            $action = 'threedsecure';
        }

        $IntegrationType = $this->payzoneGateway->getIntegrationType();
        $SecretKey = $this->payzoneGateway->getSecretKey();
        $HashMethod = $this->payzoneGateway->getHashMethod();
        if (IntegrationType::DIRECT == $IntegrationType) {
            switch ($action) {
                case 'threedsecure':
                    $this->threadProcess($this->payzoneGateway, $certDir);
                    /*<script>
                    window.parent.postMessage({'option':'iframesrc','value':'three-response'},"<?php echo $this->payzoneHelper->getSiteSecureURL(
                        'root'
                    ); ?>");
                    window.parent.postMessage({'option':'threedresponse','value':'<?php echo json_encode(
                        $paymentResponse
                    ); ?>'},"<?php echo $this->payzoneHelper->getSiteSecureURL('root'); ?>");
                </script>*/

                    break;
                case 'process':
                    $currencyCode = $this->payzoneGateway->getCurrencyCode();
                    $respobj = [];
                    $queryObj = [];
                    $queryObj['Amount'] = $paymentData->getFullAmount();
                    $queryObj['CurrencyCode'] = $currencyCode;
                    $queryObj['OrderID'] = $paymentData->getOrderId();
                    $queryObj['OrderDescription'] = 'Order description';
                    $queryObj['HashMethod'] = $HashMethod;
                    $StringToHash = $this->payzoneHelper->generateStringToHashDirect(
                        $paymentData->getFullAmount(),
                        $currencyCode,
                        $paymentData->getOrderId(),
                        'Order description',
                        $SecretKey
                    );
                    $ShoppingCartHashDigest = $this->payzoneHelper->calculateHashDigest(
                        $StringToHash,
                        $SecretKey,
                        $HashMethod
                    );
                    $ShoppingCartValidation = $this->payzoneHelper->checkIntegrityOfIncomingVariablesDirect(
                        'SHOPPING_CART_CHECKOUT',
                        $queryObj,
                        $ShoppingCartHashDigest,
                        $SecretKey
                    );
                    $respobj[
                        'ShoppingCartHashDigest'
                    ] = $ShoppingCartHashDigest;
                    if ($ShoppingCartValidation) {
                        $queryObj = $this->payzoneGateway->buildXHRequest();

                        $paymentResponse = $this->directProcess(
                            $this->payzoneGateway,
                            $queryObj,
                            $certDir
                        );

                        if (3 == $paymentResponse['StatusCode']) {
                            $callbackUrl = $successUrl;
                        } else {
                            $callbackUrl = $errorUrl;
                        }

                        $transactionDetails = [
                            'hashDigest' => $ShoppingCartHashDigest,
                            'transactionDateTime' => $paymentData->getTransactionDateTime(),
                            'callbackURL' => $callbackUrl,
                            'orderID' => $paymentData->getOrderId(),
                            'orderDescription' => 'Order description',
                            'currencyCode' => $this->payzoneGateway->getCurrencyCode(),
                            'fullAmount' => $paymentData->getFullAmount(),
                            'amount' => $paymentData->getFullAmount(),
                        ];

                        $paymentResponse[
                            'transactionDetails'
                        ] = $transactionDetails;

                        return $paymentResponse;
                    } else {
                        $paymentResponse['ErrorMessage'] =
                            'Hash mismatch validation failure';
                        $paymentResponse['ErrorMessages'] = true;

                        return $paymentResponse;
                    }
                    break;
                case 'refund':
                    $CurrencyCode = $this->payzoneGateway->getCurrencyCode();
                    $respobj = [];
                    $queryObj = [];
                    $queryObj['Amount'] = $_GET['pzgamt'];
                    $queryObj['CurrencyCode'] = $CurrencyCode;
                    $queryObj['OrderID'] = $_GET['pzgorderid'];
                    $queryObj['CrossReference'] = $_GET['pzgcrossref'];
                    $queryObj[
                        'OrderDescription'
                    ] = $paymentData->getOrderDescription();
                    $queryObj['HashMethod'] = $HashMethod;
                    $StringToHash = $this->payzoneHelper->generateStringToHashDirect(
                        $queryObj['Amount'],
                        $CurrencyCode,
                        $queryObj['OrderID'],
                        $queryObj['OrderDescription'],
                        $SecretKey
                    );
                    $ShoppingCartHashDigest = $this->payzoneHelper->calculateHashDigest(
                        $StringToHash,
                        $SecretKey,
                        $HashMethod
                    );
                    $ShoppingCartValidation = $this->payzoneHelper->checkIntegrityOfIncomingVariablesDirect(
                        'SHOPPING_CART_CHECKOUT',
                        $queryObj,
                        $ShoppingCartHashDigest,
                        $SecretKey
                    );
                    $respobj[
                        'ShoppingCartHashDigest'
                    ] = $ShoppingCartHashDigest;
                    if ($ShoppingCartValidation) {
                        $queryObj = $this->payzoneGateway->buildXHRefund();
                        //Process the transaction

                        //$this->refundProcess($queryObj);
                        return $paymentResponse;
                    } else {
                        $paymentResponse['ErrorMessage'] =
                            'Hash mismatch validation failure';
                        $paymentResponse['ErrorMessages'] = true;

                        return $paymentResponse;
                    }
                    break;

                default:
                    break;
            }
        }
    }

    protected function directProcess(
        PayzoneGateway $payzoneGateway,
        array $queryObj,
        string $certDir
    ) {
        $rgeplRequestGatewayEntryPointList = new RequestGatewayEntryPointList();

        $rgeplRequestGatewayEntryPointList->add(
            'https://gw1.payzoneonlinepayments.com:4430/',
            100,
            1
        );
        $rgeplRequestGatewayEntryPointList->add(
            'https://gw2.payzoneonlinepayments.com:4430/',
            200,
            1
        );
        $rgeplRequestGatewayEntryPointList->add(
            'https://gw3.payzoneonlinepayments.com:4430/',
            300,
            1
        );

        if (false != $queryObj['CrossReferenceTransaction']) {
            $cdtCardDetailsTransaction = new CrossReferenceTransaction(
                $rgeplRequestGatewayEntryPointList
            );
        } else {
            $cdtCardDetailsTransaction = new CardDetailsTransaction(
                $rgeplRequestGatewayEntryPointList
            );
            $cdtCardDetailsTransaction->setCertDir($certDir);
        }

        $cdtCardDetailsTransaction
            ->getMerchantAuthentication()
            ->setMerchantID($payzoneGateway->getMerchantID());
        $cdtCardDetailsTransaction
            ->getMerchantAuthentication()
            ->setPassword($payzoneGateway->getMerchantPassword());

        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getMessageDetails()
            ->setTransactionType($payzoneGateway->getTransactionType());

        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getAmount()
            ->setValue($queryObj['Amount']);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getCurrencyCode()
            ->setValue($queryObj['CurrencyCode']);

        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->setOrderID($queryObj['OrderID']);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->setOrderDescription($queryObj['OrderDescription']);

        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getTransactionControl()
            ->getEchoCardType()
            ->setValue($queryObj['EchoCardType']);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getTransactionControl()
            ->getEchoAmountReceived()
            ->setValue(true);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getTransactionControl()
            ->getEchoAVSCheckResult()
            ->setValue($queryObj['EchoAVSCheckResult']);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getTransactionControl()
            ->getEchoCV2CheckResult()
            ->setValue($queryObj['EchoCV2CheckResult']);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getTransactionControl()
            ->getThreeDSecureOverridePolicy()
            ->setValue(true);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getTransactionControl()
            ->getDuplicateDelay()
            ->setValue(60);

        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getThreeDSecureBrowserDetails()
            ->getDeviceCategory()
            ->setValue(0);
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getThreeDSecureBrowserDetails()
            ->setAcceptHeaders('*/*');
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getThreeDSecureBrowserDetails()
            ->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        if (false != $queryObj['CrossReferenceTransaction']) {
            $cdtCardDetailsTransaction
                ->getTransactionDetails()
                ->getMessageDetails()
                ->setCrossReference($queryObj['CrossReferenceTransactionID']);
            $cdtCardDetailsTransaction
                ->getOverrideCardDetails()
                ->setCV2($queryObj['CV2']);
            $cdtCardDetailsTransaction
                ->getTransactionDetails()
                ->getTransactionControl()
                ->getThreeDSecureOverridePolicy()
                ->setValue(true);
        } else {
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->setCardName($queryObj['CustomerName']);
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->setCardNumber($queryObj['CardNumber']);
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->getExpiryDate()
                ->getMonth()
                ->setValue($queryObj['ExpiryDateMonth']);
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->getExpiryDate()
                ->getYear()
                ->setValue($queryObj['ExpiryDateYear']);
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->getStartDate()
                ->getMonth()
                ->setValue($queryObj['ExpiryDateMonth']);
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->getStartDate()
                ->getYear()
                ->setValue($queryObj['ExpiryDateMonth']);
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->setIssueNumber($queryObj['IssueNumber']);
            $cdtCardDetailsTransaction
                ->getCardDetails()
                ->setCV2($queryObj['CV2']);
        }

        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->setAddress1($queryObj['Address1']);
        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->setAddress2($queryObj['Address2']);
        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->setAddress3($queryObj['Address3']);
        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->setAddress4($queryObj['Address4']);
        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->setCity($queryObj['City']);
        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->setState($queryObj['State']);
        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->setPostCode($queryObj['PostCode']);

        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->getBillingAddress()
            ->getCountryCode()
            ->setValue($queryObj['CountryCode']);

        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->setEmailAddress($queryObj['EmailAddress']);
        $cdtCardDetailsTransaction->getCustomerDetails()->setPhoneNumber('');
        $cdtCardDetailsTransaction
            ->getCustomerDetails()
            ->setCustomerIPAddress('');
        $cdtCardDetailsTransaction
            ->getTransactionDetails()
            ->getTransactionControl()
            ->getThreeDSecureOverridePolicy()
            ->setValue(true);

        $boTransactionProcessed = $cdtCardDetailsTransaction->processTransaction(
            $cdtrCardDetailsTransactionResult,
            $todTransactionOutputData
        );
        $paymentResponse = [];

        if (false == $boTransactionProcessed) {
            // could not communicate with the payment gateway
            $paymentResponse['Message'] =
                'Unable to communicate with the payment gateway.';
            $paymentResponse['StatusCode'] = '99';
        } else {
            $paymentResponse[
                'Message'
            ] = $cdtrCardDetailsTransactionResult->getMessage();
            $paymentResponse[
                'StatusCode'
            ] = $cdtrCardDetailsTransactionResult->getStatusCode();

            switch ($cdtrCardDetailsTransactionResult->getStatusCode()) {
                case 0:
                    // status code of 0 - means transaction successful
                    $paymentResponse[
                        'CrossReference'
                    ] = $todTransactionOutputData->getCrossReference();
                    break;
                case 3:
                    // status code of 3 - means 3D Secure authentication required
                    $paymentResponse['ThreeDSecure'] = true;
                    $paymentResponse[
                        'CrossReference'
                    ] = $todTransactionOutputData->getCrossReference();
                    $paymentResponse[
                        'PaREQ'
                    ] = $todTransactionOutputData
                        ->getThreeDSecureOutputData()
                        ->getPaREQ();
                    $paymentResponse[
                        'ACSURL'
                    ] = $todTransactionOutputData
                        ->getThreeDSecureOutputData()
                        ->getACSURL();
                    $paymentResponse['TermUrl'] = $payzoneGateway->getURL(
                        'process-payment'
                    );
                    break;
                case 4:
                case 5:
                    // status code of 5 - means transaction declined
                    $paymentResponse[
                        'CrossReference'
                    ] = $todTransactionOutputData->getCrossReference();
                    break;
                case 20:
                    // status code of 20 - means duplicate transaction
                    $paymentResponse[
                        'CrossReference'
                    ] = $todTransactionOutputData->getCrossReference();
                    $paymentResponse[
                        'PreviousTransactionMessage'
                    ] = $cdtrCardDetailsTransactionResult
                        ->getPreviousTransactionResult()
                        ->getMessage();
                    $paymentResponse['DuplicateTransaction'] = true;
                    break;
                case 30:
                    $paymentResponse['ErrorMessages'] = '';
                    // status code of 30 - means an error occurred
                    $eCount = $cdtrCardDetailsTransactionResult
                        ->getErrorMessages()
                        ->getCount();
                    if ($eCount > 0) {
                        for ($i = 0; $i < $eCount; ++$i) {
                            $paymentResponse[
                                'ErrorMessages'
                            ] .= $cdtrCardDetailsTransactionResult
                                ->getErrorMessages()
                                ->getAt($i);
                        }
                    }
                    break;
                default:
                    // unhandled status code

                    break;
            }
        }

        return $paymentResponse;
    }

    protected function threadProcess(
        PayzoneGateway $payzoneGateway,
        PaymentData $paymentData,
        string $certDir
    ) {
        $rgeplRequestGatewayEntryPointList = new RequestGatewayEntryPointList();

        $rgeplRequestGatewayEntryPointList->add(
            'https://gw1.payzoneonlinepayments.com:4430/',
            100,
            1
        );

        $rgeplRequestGatewayEntryPointList->add(
            'https://gw2.payzoneonlinepayments.com:4430/',
            200,
            1
        );

        $rgeplRequestGatewayEntryPointList->add(
            'https://gw3.payzoneonlinepayments.com:4430/',
            300,
            1
        );

        $tdsaThreeDSecureAuthentication = new ThreeDSecureAuthentication(
            $rgeplRequestGatewayEntryPointList
        );

        $tdsaThreeDSecureAuthentication
            ->getMerchantAuthentication()
            ->setMerchantID($payzoneGateway->getMerchantID());
        $tdsaThreeDSecureAuthentication
            ->getMerchantAuthentication()
            ->setPassword($payzoneGateway->getMerchantPassword());

        $tdsaThreeDSecureAuthentication
            ->getThreeDSecureInputData()
            ->setCrossReference($paymentData->getMd());
        $tdsaThreeDSecureAuthentication
            ->getThreeDSecureInputData()
            ->setPaRES($paymentData->getPaRes());
        $tdsaThreeDSecureAuthentication->setCertDir($certDir);
        $boTransactionProcessed = $tdsaThreeDSecureAuthentication->processTransaction(
            $tdsarThreeDSecureAuthenticationResult,
            $todTransactionOutputData
        );

        $paymentResponse = [];
        if (false == $boTransactionProcessed) {
            // could not communicate with the payment gateway
            $paymentResponse['Message'] =
                'Unable to communicate with the payment gateway.';
            $paymentResponse['StatusCode'] = '99';
        } else {
            $paymentResponse[
                'Message'
            ] = $tdsarThreeDSecureAuthenticationResult->getMessage();
            $paymentResponse[
                'StatusCode'
            ] = $tdsarThreeDSecureAuthenticationResult->getStatusCode();

            switch ($tdsarThreeDSecureAuthenticationResult->getStatusCode()) {
                case 0:
                    // status code of 0 - means transaction successful
                    $paymentResponse[
                        'CrossReference'
                    ] = $todTransactionOutputData->getCrossReference();
                    break;
                case 5:
                    // status code of 5 - means transaction declined
                    $paymentResponse[
                        'CrossReference'
                    ] = $todTransactionOutputData->getCrossReference();
                    break;
                case 20:
                    // status code of 20 - means duplicate transaction
                    $paymentResponse[
                        'CrossReference'
                    ] = $todTransactionOutputData->getCrossReference();
                    $paymentResponse[
                        'PreviousTransactionMessage'
                    ] = $tdsarThreeDSecureAuthenticationResult
                        ->getPreviousTransactionResult()
                        ->getMessage();
                    $paymentResponse['DuplicateTransaction'] = true;
                    break;
                case 30:
                    // status code of 30 - means an error occurred
                    $paymentResponse['ErrorMessages'] = '';
                    // status code of 30 - means an error occurred
                    $eCount = $cdtrCardDetailsTransactionResul
                        ->getErrorMessages()
                        ->getCount();
                    if ($eCount > 0) {
                        for ($i = 0; $i < $eCount; ++$i) {
                            $paymentResponse[
                                'ErrorMessages'
                            ] .= $cdtrCardDetailsTransactionResult
                                ->getErrorMessages()
                                ->getAt($i);
                        }
                    }
                    break;
                default:
                    // unhandled status code

                    break;
            }
        }

        return $paymentResponse;
    }

    protected function getConfiguration()
    {
        $config = new Configuration();

        $merchantId = $this->getParameter('app.paymentgateway.merchantid');
        $password = $this->getParameter('app.paymentgateway.password');
        $presharedkey = $this->getParameter('app.paymentgateway.presharedkey');
        $secretkey = $this->getParameter('app.paymentgateway.secretkey');
        $integrationtype = $this->getParameter(
            'app.paymentgateway.integrationtype'
        );
        $hashmethod = $this->getParameter('app.paymentgateway.hashmethod');
        $transactiontype = $this->getParameter(
            'app.paymentgateway.transactiontype'
        );
        $currencycode = $this->getParameter('app.paymentgateway.currencycode');
        $hostediframe = $this->getParameter('app.paymentgateway.hostediframe');
        $hostedcustomerdetails = $this->getParameter(
            'app.paymentgateway.hostedcustomerdetails'
        );
        $payzoneimages = $this->getParameter(
            'app.paymentgateway.payzoneimages'
        );
        $orderdetails = $this->getParameter('app.paymentgateway.orderdetails');

        $config->setMerchantId($merchantId);
        $config->setMerchantPassword($password);
        $config->setPreSharedKey($presharedkey);
        $config->setSecretKey($secretkey);
        $config->setIntegrationType($integrationtype);
        $config->setHashMethod($hashmethod);
        $config->setTransactionType($transactiontype);
        $config->setCurrencyCode($currencycode);
        $config->setHostedIframe($hostediframe);
        $config->setHostedCustomerDetails($hostedcustomerdetails);
        $config->setPayzoneImages($payzoneimages);
        $config->setOrderDetails($orderdetails);

        return $config;
    }

    public function callback(
        Request $request,
        $transactionService,
        EntityManagerInterface $em
    ) {
        $validate = $this->payzoneGateway->validateResponse($_GET, $_POST);
        $responsePage = ResultController::class;

        if (isset($validate['Notification'])) {
            //Check if the order has been processed, success or fail a Notification array will be added from the validate function
            $showresults = true; // set show results to true, we can hide the results if we need to for 3d secure handling etc
            //#### DEVELOPER NOTICE #####
            /*
            This section allows you to add in additional functions for specific scenarios with a completed tranasction, this section is left blank apart from the 3D secure transacation handler
            */
            //###########################
            switch ($validate['Notification']['Type']) {
                case PayzoneResponseOutcomes::SUCCESS: // Payment successful
                    $iframesrc = 'results';

                    $this->confirmInvestment(
                        $transactionService,
                        $validate['Order']['OrderID'],
                        $em
                    );
                    $this->confirmEntryFee(
                        $transactionService,
                        $validate['Order']['OrderID'],
                        $em
                    );
                    $this->confirmReserve($validate['Order']['OrderID'], $em);

                    break;
                case PayzoneResponseOutcomes::DECLINED:
                    $iframesrc = 'results';
                    $this->removeTransaction(
                        $validate['Order']['OrderID'],
                        $em
                    );
                    break;
                case PayzoneResponseOutcomes::THREED:
                    $iframesrc = 'results-threed';
                    //3D Secure Authentication required - don't display results yet but pass over to 3D secure handler
                    $showresults = false;
                    break;
                case PayzoneResponseOutcomes::DUPLICATE:
                    if (array_key_exists('Order', $validate)) {
                        $this->removeTransaction(
                            $validate['Order']['OrderID'],
                            $em
                        );
                    }
                    $iframesrc = 'results';
                    break;
                case PayzoneResponseOutcomes::ERROR:
                    if (array_key_exists('Order', $validate)) {
                        $this->removeTransaction(
                            $validate['Order']['OrderID'],
                            $em
                        );
                    }
                    $iframesrc = 'results';
                    break;
                case PayzoneResponseOutcomes::UNKNOWN:
                    if (array_key_exists('Order', $validate)) {
                        $this->removeTransaction(
                            $validate['Order']['OrderID'],
                            $em
                        );
                    }
                    $iframesrc = 'results';
                    break;
                default:
                    $iframesrc = 'results';
                    if (array_key_exists('Order', $validate)) {
                        $this->removeTransaction(
                            $validate['Order']['OrderID'],
                            $em
                        );
                    }
                    break;
            }

            if ($showresults) {
                if (
                    PayzoneResponseOutcomes::SUCCESS ==
                        $validate['Notification']['Type'] ||
                    PayzoneResponseOutcomes::DECLINED ==
                        $validate['Notification']['Type'] ||
                    PayzoneResponseOutcomes::DUPLICATE ==
                        $validate['Notification']['Type'] ||
                    PayzoneResponseOutcomes::THREED ==
                        $validate['Notification']['Type'] ||
                    PayzoneResponseOutcomes::UNKNOWN ==
                        $validate['Notification']['Type']
                ) {
                    $responsePage .= '::default';
                } elseif (
                    PayzoneResponseOutcomes::ERROR ==
                    $validate['Notification']['Type']
                ) {
                    $responsePage .= '::error';
                }

                $validate['responsePage'] = $responsePage;
            }
        }

        return $validate;
    }

    public function confirmInvestment(
        TransactionService $service,
        string $referenceCode,
        EntityManagerInterface $em
    ) {
        $investment = $em->getRepository(Investment::class)->findOneBy([
            'referenceCode' => $referenceCode,
        ]);

        if ($investment) {
            $service->process($investment->getTransaction(), function (
                EntityManagerInterface $em,
                Transaction $transaction
            ) use ($investment) {
                $investment
                    ->setApprovedAt($transaction->getProcessedAt())
                    ->setStatus(InvestmentStatus::APPROVED());

                $em->persist($investment);
                $em->flush();
            });
        }
    }

    public function removeInvestment(
        string $referenceCode,
        EntityManagerInterface $em
    ) {
        $investment = $em->getRepository(Investment::class)->findOneBy([
            'referenceCode' => $referenceCode,
        ]);

        if ($investment) {
            $em->remove($investment);
            $em->flush();
        }
    }

    public function confirmReserve(
        string $referenceCode,
        EntityManagerInterface $em
    ) {
        /**
         * @var Reserve
         */
        $reserve = $em->getRepository(Reserve::class)->findOneBy([
            'referenceCode' => $referenceCode,
        ]);

        if ($reserve) {
            $reserve->setProcessedAt(new DateTime());

            $em->persist($reserve);
            $em->flush();
        }
    }

    public function removeReserve(
        string $referenceCode,
        EntityManagerInterface $em
    ) {
        /**
         * @var Reserve
         */
        $reserve = $em->getRepository(Reserve::class)->findOneBy([
            'referenceCode' => $referenceCode,
        ]);

        if ($reserve) {
            $em->remove($reserve);
            $em->flush();
        }
    }

    public function confirmEntryFee(
        TransactionService $service,
        string $referenceCode,
        EntityManagerInterface $em
    ) {
        /**
         * @var EntryFee
         */
        $entryFee = $em->getRepository(EntryFee::class)->findOneBy([
            'referenceCode' => $referenceCode,
        ]);

        if ($entryFee) {
            $service->process($entryFee->getTransaction(), function (
                EntityManagerInterface $em,
                Transaction $transaction
            ) use ($entryFee) {
                $entryFee
                    ->setApprovedAt($transaction->getProcessedAt())
                    ->setStatus(InvestmentStatus::APPROVED());

                $em->persist($entryFee);
                $em->flush();
            });
        }
    }

    public function removeEntryFee(
        string $referenceCode,
        EntityManagerInterface $em
    ) {
        /**
         * @var EntryFee
         */
        $entryFee = $em->getRepository(EntryFee::class)->findOneBy([
            'referenceCode' => $referenceCode,
        ]);

        if ($entryFee) {
            $em->remove($entryFee);
            $em->flush();
        }
    }

    public function removeTransaction(
        string $referenceCode,
        EntityManagerInterface $em
    ) {
        $this->removeInvestment($referenceCode, $em);
        $this->removeEntryFee($referenceCode, $em);
        $this->removeReserve($referenceCode, $em);
    }

    public function validatePaymentResponse(
        Request $request,
        TransactionService $transactionService,
        EntityManagerInterface $em
    ) {
        $paymentData = new PaymentData();
        $config = $this->getConfiguration();
        $this->payzoneGateway = new PayzoneGateway($paymentData, $em, $config);

        $validate = $this->callback($request, $transactionService, $em);

        return $validate;
    }
}
