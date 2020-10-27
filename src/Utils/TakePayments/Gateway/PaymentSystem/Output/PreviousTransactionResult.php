<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\NullableInt;

class PreviousTransactionResult
{
    /**
     * @var NullableInt
     */
    private $m_nStatusCode;
    /**
     * @var string
     */
    private $m_szMessage;

    /**
     * @return NullableInt
     */
    public function getStatusCode()
    {
        return $this->m_nStatusCode;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->m_szMessage;
    }

    /**
     * @param NullableInt $nStatusCode
     * @param string      $szMessage
     */
    public function __construct(NullableInt $nStatusCode = null, $szMessage)
    {
        $this->m_nStatusCode = $nStatusCode;
        $this->m_szMessage = $szMessage;
    }
}
