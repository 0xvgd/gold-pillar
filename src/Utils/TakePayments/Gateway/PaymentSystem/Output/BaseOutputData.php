<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

class BaseOutputData
{
    /**
     * @var GatewayEntryPointList
     */
    private $m_lgepGatewayEntryPoints;

    /**
     * @return GatewayEntryPointList
     */
    public function getGatewayEntryPoints()
    {
        return $this->m_lgepGatewayEntryPoints;
    }

    //constructor

    /**
     * @param GatewayEntryPointList $lgepGatewayEntryPoints
     */
    public function __construct(
        GatewayEntryPointList $lgepGatewayEntryPoints = null
    ) {
        $this->m_lgepGatewayEntryPoints = $lgepGatewayEntryPoints;
    }
}
