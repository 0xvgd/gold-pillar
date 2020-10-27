<?php

namespace App\Exception;

use Exception;

final class InsufficientBalanceException extends Exception
{
    public function __construct()
    {
        parent::__construct('Insufficient balance');
    }
}
