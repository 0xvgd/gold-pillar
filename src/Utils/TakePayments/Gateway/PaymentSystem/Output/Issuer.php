<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\NullableInt;

class Issuer
{
    /**
     * @var string
     */
    private $m_szIssuer;
    /**
     * @var NullableInt
     */
    private $m_nISOCode;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->m_szIssuer;
    }

    /**
     * @return NullableInt
     */
    public function getISOCode()
    {
        return $this->m_nISOCode;
    }

    //constructor
    public function __construct($szIssuer, NullableInt $nISOCode = null)
    {
        $this->m_szIssuer = $szIssuer;
        $this->m_nISOCode = $nISOCode;
    }
}
