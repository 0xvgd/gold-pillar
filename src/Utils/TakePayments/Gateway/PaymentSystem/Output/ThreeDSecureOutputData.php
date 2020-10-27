<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

class ThreeDSecureOutputData
{
    /**
     * @var string
     */
    private $m_szPaREQ;
    /**
     * @var string
     */
    private $m_szACSURL;

    /**
     * @return string
     */
    public function getPaREQ()
    {
        return $this->m_szPaREQ;
    }

    /**
     * @return string
     */
    public function getACSURL()
    {
        return $this->m_szACSURL;
    }

    //constructor

    /**
     * @param string $szPaREQ
     * @param string $szACSURL
     */
    public function __construct($szPaREQ, $szACSURL)
    {
        $this->m_szPaREQ = $szPaREQ;
        $this->m_szACSURL = $szACSURL;
    }
}
