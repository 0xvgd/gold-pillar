<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use App\Utils\TakePayments\Gateway\Common\NullableInt;

class ThreeDSecureBrowserDetails
{
    /**
     * @var NullableInt
     */
    private $m_nDeviceCategory;
    /**
     * @var string
     */
    private $m_szAcceptHeaders;
    /**
     * @var string
     */
    private $m_szUserAgent;

    /**
     * @return NullableInt
     */
    public function getDeviceCategory()
    {
        return $this->m_nDeviceCategory;
    }

    /**
     * @return string
     */
    public function getAcceptHeaders()
    {
        return $this->m_szAcceptHeaders;
    }

    /**
     * @param string $acceptHeaders
     */
    public function setAcceptHeaders($acceptHeaders)
    {
        $this->m_szAcceptHeaders = $acceptHeaders;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->m_szUserAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->m_szUserAgent = $userAgent;
    }

    //constructor
    public function __construct()
    {
        $this->m_nDeviceCategory = new NullableInt();
        $this->m_szAcceptHeaders = '';
        $this->m_szUserAgent = '';
    }
}
