<?php

namespace App\Utils\TakePayments\Gateway\Common;

class ISOCountryList
{
    private $m_licISOCountries;

    public function getISOCountry($type, $country)
    {
        $result = false;
        $nCount = 0;
        $icISOCountry = null;

        while (!$result && $nCount < count($this->m_licISOCountries)) {
            $icISOCountry = $this->m_licISOCountries[$nCount];
            switch ($type) {
                case 'ISO':
                    if ($country == $icISOCountry->getISOCode()) {
                        $result = $icISOCountry->getCountryName();
                    }
                    break;
                case 'Short':
                    if ($country == $icISOCountry->getCountryShort()) {
                        $result = $icISOCountry->getISOCode();
                    }
                    break;
                case 'Name':
                    if ($country == $icISOCountry->getCountryName()) {
                        $result = $icISOCountry->getISOCode();
                    }
                    break;
                default:
                    //ISO code
                    if ($country == $icISOCountry->getISOCode()) {
                        $result = $icISOCountry->getCountryName();
                    }
                    break;
            }

            ++$nCount;
        }

        return $result;
    }

    public function getCount()
    {
        return count($this->m_licISOCountries);
    }

    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_licISOCountries)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_licISOCountries[$nIndex];
    }

    public function add(
        $nISOCode,
        $szCountryName,
        $szCountryShort,
        $nListPriority,
        $nISO2Alpha
    ) {
        $newISOCountry = new ISOCountry(
            $nISOCode,
            $szCountryName,
            $szCountryShort,
            $nListPriority,
            $nISO2Alpha
        );

        $this->m_licISOCountries[] = $newISOCountry;
    }

    //constructor
    public function __construct()
    {
        $this->m_licISOCountries = [];
    }
}
