<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages;

use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GetGatewayEntryPointsOutputData;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GetGatewayEntryPointsResult;
use App\Utils\TakePayments\Gateway\SOAP\SOAP;

class GetGatewayEntryPoints extends GatewayTransaction
{
    /**
     * @param GetGatewayEntryPointsResult     $ggeprGetGatewayEntryPointsResult    Passed as reference
     * @param GetGatewayEntryPointsOutputData $ggepGetGatewayEntryPointsOutputData Passed as reference
     *
     * @return bool
     *
     * @throws Exception
     */
    public function processTransaction(
        GetGatewayEntryPointsResult &$ggeprGetGatewayEntryPointsResult = null,
        GetGatewayEntryPointsOutputData &$ggepGetGatewayEntryPointsOutputData = null
    ) {
        $boTransactionSubmitted = false;
        $sSOAPClient = null;
        $lgepGatewayEntryPoints = null;

        $ggepGetGatewayEntryPointsOutputData = null;
        $goGatewayOutput = null;

        $sSOAPClient = new SOAP(
            'GetGatewayEntryPoints',
            GatewayTransaction::getSOAPNamespace()
        );

        $boTransactionSubmitted = GatewayTransaction::processTransactionBase(
            $sSOAPClient,
            'GetGatewayEntryPointsMessage',
            'GetGatewayEntryPointsResult',
            'GetGatewayEntryPointsOutputData',
            $goGatewayOutput,
            $lgepGatewayEntryPoints
        );

        if ($boTransactionSubmitted) {
            $ggeprGetGatewayEntryPointsResult = $goGatewayOutput;

            $ggepGetGatewayEntryPointsOutputData = new GetGatewayEntryPointsOutputData(
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
        if (null == $nRetryAttempts && null == $nTimeout) {
            GatewayTransaction::__construct(
                $lrgepRequestGatewayEntryPoints,
                1,
                null
            );
        } else {
            GatewayTransaction::__construct(
                $lrgepRequestGatewayEntryPoints,
                $nRetryAttempts,
                $nTimeout
            );
        }
    }
}
