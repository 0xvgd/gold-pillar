<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

class ThreeDSecureInputData
{
    /**
     * @var string
     */
    private $m_szCrossReference;
    /**
     * @var string
     */
    private $m_szPaRES;

    /**
     * @return string
     */
    public function getCrossReference()
    {
        return $this->m_szCrossReference;
    }

    /**
     * @param string $crossReference
     */
    public function setCrossReference($crossReference)
    {
        $this->m_szCrossReference = $crossReference;
    }

    /**
     * @return string
     */
    public function getPaRES()
    {
        return $this->m_szPaRES;
    }

    /**
     * @param string $PaRES
     */
    public function setPaRES($PaRES)
    {
        $this->m_szPaRES = $PaRES;
    }

    //constructor
    public function __construct()
    {
        $this->m_szCrossReference = '';
        $this->m_szPaRES = '';
    }
}
