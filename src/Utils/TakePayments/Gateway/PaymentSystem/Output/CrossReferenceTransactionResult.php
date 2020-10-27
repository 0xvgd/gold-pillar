<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\NullableBool;
use App\Utils\TakePayments\Gateway\Common\StringList;

class CrossReferenceTransactionResult extends PaymentMessageGatewayOutput
{
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
        parent::__construct(
            $nStatusCode,
            $szMessage,
            $boAuthorisationAttempted,
            $ptdPreviousTransactionResult,
            $lszErrorMessages
        );
    }
}
