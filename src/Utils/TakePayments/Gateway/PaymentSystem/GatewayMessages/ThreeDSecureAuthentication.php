<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages;

use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\Common\SharedFunctions;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\ThreeDSecureInputData;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\ThreeDSecureAuthenticationResult;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\TransactionOutputData;
use App\Utils\TakePayments\Gateway\SOAP\SOAP;

class ThreeDSecureAuthentication extends GatewayTransaction
{
    /**
     * @var ThreeDSecureInputData
     */
    private $m_tdsidThreeDSecureInputData;

    /**
     * @return ThreeDSecureInputData
     */
    public function getThreeDSecureInputData()
    {
        return $this->m_tdsidThreeDSecureInputData;
    }

    /**
     * @param ThreeDSecureAuthenticationResult $tdsarThreeDSecureAuthenticationResult Passed by reference
     * @param TransactionOutputData            $todTransactionOutputData              Passed by reference
     *
     * @return bool
     *
     * @throws Exception
     */
    public function processTransaction(
        ThreeDSecureAuthenticationResult &$tdsarThreeDSecureAuthenticationResult = null,
        TransactionOutputData &$todTransactionOutputData = null
    ) {
        $boTransactionSubmitted = false;
        $sSOAPClient = null;
        $lgepGatewayEntryPoints = null;

        $todTransactionOutputData = null;
        $goGatewayOutput = null;

        $sSOAPClient = new SOAP(
            'ThreeDSecureAuthentication',
            GatewayTransaction::getSOAPNamespace(),
            $this->getCertDir()
        );
        if (null != $this->m_tdsidThreeDSecureInputData) {
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_tdsidThreeDSecureInputData->getCrossReference()
            )
            ) {
                $sSOAPClient->addParamAttribute(
                    'ThreeDSecureMessage.ThreeDSecureInputData',
                    'CrossReference',
                    $this->m_tdsidThreeDSecureInputData->getCrossReference()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_tdsidThreeDSecureInputData->getPaRES()
            )
            ) {
                $sSOAPClient->addParam(
                    'ThreeDSecureMessage.ThreeDSecureInputData.PaRES',
                    $this->m_tdsidThreeDSecureInputData->getPaRES()
                );
            }
        }

        $boTransactionSubmitted = GatewayTransaction::processTransactionBase(
            $sSOAPClient,
            'ThreeDSecureMessage',
            'ThreeDSecureAuthenticationResult',
            'TransactionOutputData',
            $goGatewayOutput,
            $lgepGatewayEntryPoints
        );

        if ($boTransactionSubmitted) {
            $tdsarThreeDSecureAuthenticationResult = SharedFunctionsPaymentSystemShared::getPaymentMessageGatewayOutput(
                $sSOAPClient->getXmlTag(),
                'ThreeDSecureAuthenticationResult',
                $goGatewayOutput
            );

            $todTransactionOutputData = SharedFunctionsPaymentSystemShared::getTransactionOutputData(
                $sSOAPClient->getXmlTag(),
                $lgepGatewayEntryPoints
            );
        }

        return $boTransactionSubmitted;
    }

    //constructor

    /**
     * @param RequestGatewayEntryPointList $lrgepRequestGatewayEntryPoints
     * @param int                          $nRetryAttempts
     * @param NullableInt                  $nTimeout
     */
    public function __construct(
        RequestGatewayEntryPointList $lrgepRequestGatewayEntryPoints = null,
        $nRetryAttempts = 1,
        NullableInt $nTimeout = null
    ) {
        GatewayTransaction::__construct(
            $lrgepRequestGatewayEntryPoints,
            $nRetryAttempts,
            $nTimeout
        );

        $this->m_tdsidThreeDSecureInputData = new ThreeDSecureInputData();
    }
}
