<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\StringList;

class GetCardTypeResult extends GatewayOutput
{
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
        parent::__construct($nStatusCode, $szMessage, $lszErrorMessages);
    }
}
