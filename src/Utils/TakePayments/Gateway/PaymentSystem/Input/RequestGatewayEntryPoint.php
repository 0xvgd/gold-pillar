<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GatewayEntryPoint;

class RequestGatewayEntryPoint extends GatewayEntryPoint
{
    /**
     * @var int
     */
    private $m_nRetryAttempts;

    /**
     * @return int
     */
    public function getRetryAttempts()
    {
        return $this->m_nRetryAttempts;
    }

    /**
     * @param string $szEntryPointURL
     * @param int    $nMetric
     * @param int    $nRetryAttempts
     */
    public function __construct($szEntryPointURL, $nMetric, $nRetryAttempts)
    {
        //do NOT forget to call the parent constructor too
        //parent::GatewayEntryPoint($szEntryPointURL, $nMetric);
        GatewayEntryPoint::__construct($szEntryPointURL, $nMetric);

        $this->m_nRetryAttempts = $nRetryAttempts;
    }
}
