<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

class GatewayEntryPoint
{
    /**
     * @var string
     */
    private $m_szEntryPointURL;
    /**
     * @var int
     */
    private $m_nMetric;

    /**
     * @return string
     */
    public function getEntryPointURL()
    {
        return $this->m_szEntryPointURL;
    }

    /**
     * @return int
     */
    public function getMetric()
    {
        return $this->m_nMetric;
    }

    /**
     * @return string
     */
    public function toXmlString()
    {
        $szXmlString =
            '<GatewayEntryPoint EntryPointURL="'.
            $this->m_szEntryPointURL.
            '" Metric="'.
            $this->m_nMetric.
            '" />';

        return $szXmlString;
    }

    //constructor

    /**
     * @param string $szEntryPointURL
     * @param int    $nMetric
     */
    public function __construct($szEntryPointURL, $nMetric)
    {
        $this->m_szEntryPointURL = $szEntryPointURL;
        $this->m_nMetric = $nMetric;
    }
}
