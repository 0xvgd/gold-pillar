<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\StringList;

class GatewayOutput
{
    /**
     * @var int
     */
    private $m_nStatusCode;
    /**
     * @var string
     */
    private $m_szMessage;
    /**
     * @var StringList
     */
    private $m_lszErrorMessages;

    /**
     * @return int
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
     * @return StringList
     */
    public function getErrorMessages()
    {
        return $this->m_lszErrorMessages;
    }

    //constructor

    /**
     * @param int        $nStatusCode
     * @param string     $szMessage
     * @param StringList $lszErrorMessages
     */
    public function __construct(
        $nStatusCode,
        $szMessage,
        StringList $lszErrorMessages = null
    ) {
        $this->m_nStatusCode = $nStatusCode;
        $this->m_szMessage = $szMessage;
        $this->m_lszErrorMessages = $lszErrorMessages;
    }
}
