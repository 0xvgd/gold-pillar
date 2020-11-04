<?php

namespace App\Utils\TakePayments\Gateway\Common;

use Exception;

class NullableInt extends Nullable
{
    private $m_nValue;

    public function getValue()
    {
        if (false == $this->m_boHasValue) {
            throw new Exception('Object has no value');
        }

        return $this->m_nValue;
    }

    public function setValue($nValue)
    {
        if (null == $nValue) {
            $this->m_boHasValue = false;
            $this->m_nValue = null;
        } else {
            $this->m_boHasValue = true;
            $this->m_nValue = $nValue;
        }
    }

    //constructor
    public function __construct($nValue = null)
    {
        Nullable::__construct();

        if (null != $nValue) {
            $this->setValue($nValue);
        }
    }
}
