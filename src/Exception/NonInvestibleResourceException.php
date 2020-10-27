<?php

namespace App\Exception;

use Exception;

final class NonInvestibleResourceException extends Exception
{
    public function __construct()
    {
        parent::__construct('Non investible resource');
    }
}
