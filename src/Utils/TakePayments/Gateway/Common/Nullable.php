<?php

namespace App\Utils\TakePayments\Gateway\Common;

abstract class Nullable
{
    protected $m_boHasValue;

    public function getHasValue()
    {
        return $this->m_boHasValue;
    }

    public function __construct()
    {
        $this->m_boHasValue = false;
    }
}
