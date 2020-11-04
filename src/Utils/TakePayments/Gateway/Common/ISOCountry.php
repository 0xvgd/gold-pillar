<?php

namespace App\Utils\TakePayments\Gateway\Common;

use Exception;

class ISOCountry
{
    private $m_szCountryName;
    private $m_szCountryShort;
    private $m_nISOCode;
    private $m_nListPriority;
    private $m_nISO2Alpha;

    //public properties
    public function getCountryName()
    {
        return $this->m_szCountryName;
    }

    public function getCountryShort()
    {
        return $this->m_szCountryShort;
    }

    public function getISOCode()
    {
        return $this->m_nISOCode;
    }

    public function getListPriority()
    {
        return $this->m_nListPriority;
    }

    public function getISO2Alpha()
    {
        return $this->m_nISO2Alpha;
    }

    //constructor
    public function __construct(
        $nISOCode,
        $szCountryName,
        $szCountryShort,
        $nListPriority,
        $nISO2Alpha
    ) {
        if (!is_int($nISOCode) ||
            !is_string($szCountryName) ||
            !is_string($szCountryShort) ||
            !is_int($nListPriority)
        ) {
            throw new Exception('Invalid parameter type');
        }

        $this->m_nISOCode = $nISOCode;
        $this->m_szCountryName = $szCountryName;
        $this->m_szCountryShort = $szCountryShort;
        $this->m_nListPriority = $nListPriority;
        $this->m_nISO2Alpha = $nISO2Alpha;
    }
}
