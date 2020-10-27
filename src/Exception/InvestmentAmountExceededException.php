<?php

namespace App\Exception;

use Exception;

final class InvestmentAmountExceededException extends Exception
{
    public function __construct()
    {
        parent::__construct('Investment amount exceeded');
    }
}
