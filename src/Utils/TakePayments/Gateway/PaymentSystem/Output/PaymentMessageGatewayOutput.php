<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\NullableBool;
use App\Utils\TakePayments\Gateway\Common\StringList;

class PaymentMessageGatewayOutput extends GatewayOutput
{
    /**
     * @var PreviousTransactionResult
     */
    private $m_ptdPreviousTransactionResult;
    /**
     * @var NullableBool
     */
    private $m_boAuthorisationAttempted;

    /**
     * @return PreviousTransactionResult
     */
    public function getPreviousTransactionResult()
    {
        return $this->m_ptdPreviousTransactionResult;
    }

    /**
     * @return NullableBool
     */
    public function getAuthorisationAttempted()
    {
        return $this->m_boAuthorisationAttempted;
    }

    //constructor

    /**
     * @param int                       $nStatusCode
     * @param string                    $szMessage
     * @param NullableBool              $boAuthorisationAttempted
     * @param PreviousTransactionResult $ptdPreviousTransactionResult
     * @param StringList                $lszErrorMessages
     */
    public function __construct(
        $nStatusCode,
        $szMessage,
        NullableBool $boAuthorisationAttempted = null,
        PreviousTransactionResult $ptdPreviousTransactionResult = null,
        StringList $lszErrorMessages = null
    ) {
        parent::__construct($nStatusCode, $szMessage, $lszErrorMessages);
        $this->m_boAuthorisationAttempted = $boAuthorisationAttempted;
        $this->m_ptdPreviousTransactionResult = $ptdPreviousTransactionResult;
    }
}
