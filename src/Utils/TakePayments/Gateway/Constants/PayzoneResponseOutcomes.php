<?php

namespace App\Utils\TakePayments\Gateway\Constants;

/**
 * [PAYZONE_RESPONSE_OUTCOMES constants for human readable response code mapping].
 */
class PayzoneResponseOutcomes
{
    const SUCCESS = 'Success';
    const DECLINED = 'Declined';
    const THREED = '3D Secure Required';
    const DUPLICATE = 'Duplicate';
    const ERROR = 'Error';
    const UNKNOWN = 'Unknown';
}
