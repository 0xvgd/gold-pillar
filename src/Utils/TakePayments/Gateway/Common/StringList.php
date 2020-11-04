<?php

namespace App\Utils\TakePayments\Gateway\Common;

use Exception;

class StringList
{
    private $m_lszStrings;

    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_lszStrings)) {
            throw new Exception('Array index out of bounds');
        }

        return (string) $this->m_lszStrings[$nIndex];
    }

    public function getCount()
    {
        return count($this->m_lszStrings);
    }

    public function add($szString)
    {
        if (!is_string($szString)) {
            throw new Exception('Invalid parameter type');
        }

        return $this->m_lszStrings[] = $szString;
    }

    //constructor
    public function __construct()
    {
        $this->m_lszStrings = [];
    }
}
