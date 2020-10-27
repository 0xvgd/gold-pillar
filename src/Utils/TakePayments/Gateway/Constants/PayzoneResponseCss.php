<?php

namespace App\Utils\TakePayments\Gateway\Constants;

/**
 * [PAYZONE_RESPONSE_CSS constants for human readable response code mapping].
 */
class PayzoneResponseCss
{
    const ERROR = 'payzone-error';
    const SUCCESS = 'payzone-success';
    const DECLINED = 'payzone-declined';
    const DUPLICATE = 'payzone-duplicate';
    const UNKNOWN = 'payzone-error payzone-unknown';
}
