<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

class GetGatewayEntryPointsOutputData extends BaseOutputData
{
    //constructor
    public function __construct(
        GatewayEntryPointList $lgepGatewayEntryPoints = null
    ) {
        parent::__construct($lgepGatewayEntryPoints);
    }
}
