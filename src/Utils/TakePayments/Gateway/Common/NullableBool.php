<?php

namespace App\Utils\TakePayments\Gateway\Common;

class NullableBool extends Nullable
{
    private $m_boValue;

    public function getValue()
    {
        if (false == $this->m_boHasValue) {
            throw new Exception('Object has no value');
        }

        return $this->m_boValue;
    }

    public function setValue($boValue)
    {
        if (null == $boValue) {
            $this->m_boHasValue = false;
            $this->m_boValue = null;
        } else {
            $this->m_boHasValue = true;
            $this->m_boValue = $boValue;
        }
    }

    //constructor
    public function __construct($boValue = null)
    {
        Nullable::__construct();

        if (null != $boValue) {
            $this->setValue($boValue);
        }
    }
}
