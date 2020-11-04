<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages;

use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\Common\SharedFunctions;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GetCardTypeOutputData;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GetCardTypeResult;
use App\Utils\TakePayments\Gateway\SOAP\SOAP;

class GetCardType extends GatewayTransaction
{
    /**
     * @var string
     */
    private $m_szCardNumber;

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->m_szCardNumber;
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber($cardNumber)
    {
        $this->m_szCardNumber = $cardNumber;
    }

    /**
     * @param GetCardTypeResult     $gctrGetCardTypeResult      Passed by reference
     * @param GetCardTypeOutputData $gctodGetCardTypeOutputData Passed by reference
     *
     * @return bool
     *
     * @throws Exception
     */
    public function processTransaction(
        GetCardTypeResult &$gctrGetCardTypeResult = null,
        GetCardTypeOutputData &$gctodGetCardTypeOutputData = null
    ) {
        $boTransactionSubmitted = false;
        $sSOAPClient = null;
        $lgepGatewayEntryPoints = null;
        $ctdCardTypeData = null;

        $gctodGetCardTypeOutputData = null;
        $goGatewayOutput = null;

        $sSOAPClient = new SOAP(
            'GetCardType',
            GatewayTransaction::getSOAPNamespace(),
            $this->getCertDir()
        );
        if (!SharedFunctions::isStringNullOrEmpty($this->m_szCardNumber)
        ) {
            $sSOAPClient->addParam(
                'GetCardTypeMessage.CardNumber',
                $this->m_szCardNumber
            );
        }

        $boTransactionSubmitted = GatewayTransaction::processTransactionBase(
            $sSOAPClient,
            'GetCardTypeMessage',
            'GetCardTypeResult',
            'GetCardTypeOutputData',
            $goGatewayOutput,
            $lgepGatewayEntryPoints
        );

        if ($boTransactionSubmitted) {
            $gctrGetCardTypeResult = $goGatewayOutput;

            $ctdCardTypeData = SharedFunctionsPaymentSystemShared::getCardTypeData(
                $sSOAPClient->getXmlTag(),
                'GetCardTypeOutputData.CardTypeData'
            );

            $gctodGetCardTypeOutputData = new GetCardTypeOutputData(
                $ctdCardTypeData,
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

        $this->m_szCardNumber = '';
    }
}
