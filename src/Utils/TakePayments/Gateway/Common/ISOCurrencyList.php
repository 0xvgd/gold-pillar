<?php

namespace App\Utils\TakePayments\Gateway\Common;

class ISOCurrencyList
{
    private $m_licISOCurrencies;

    public function getISOCurrency($szCurrencyShort, &$icISOCurrency)
    {
        $boFound = false;
        $nCount = 0;
        $icISOCurrency2 = null;

        $icISOCurrency = null;

        while (!$boFound && $nCount < count($this->m_licISOCurrencies)) {
            $icISOCurrency2 = $this->m_licISOCurrencies[$nCount];

            if ($szCurrencyShort == $icISOCurrency2->getCurrencyShort()) {
                $icISOCurrency = new ISOCurrency(
                    $icISOCurrency2->getISOCode(),
                    $icISOCurrency2->getCurrency(),
                    $icISOCurrency2->getCurrencyShort(),
                    $icISOCurrency2->getExponent()
                );
                $boFound = true;
            }

            ++$nCount;
        }

        return $boFound;
    }

    public function getISOCurrencyCode($szCurrencyShort)
    {
        $boFound = false;
        $nCount = 0;
        $icISOCurrency2 = null;

        $icISOCurrency = null;

        while (!$boFound && $nCount < count($this->m_licISOCurrencies)) {
            $icISOCurrency2 = $this->m_licISOCurrencies[$nCount];
            if ($szCurrencyShort == $icISOCurrency2->getCurrencyShort()) {
                $icISOCurrency = new ISOCurrency(
                    $icISOCurrency2->getISOCode(),
                    $icISOCurrency2->getCurrency(),
                    $icISOCurrency2->getCurrencyShort(),
                    $icISOCurrency2->getExponent()
                );

                //$boFound = true;
                $boFound = $icISOCurrency2->getISOCode();
            }

            ++$nCount;
        }

        return $boFound;
    }

    public function getISOCurrencyShort($szCurrencyCode)
    {
        $boFound = false;
        $nCount = 0;
        $icISOCurrency2 = null;

        $icISOCurrency = null;

        while (!$boFound && $nCount < count($this->m_licISOCurrencies)) {
            $icISOCurrency2 = $this->m_licISOCurrencies[$nCount];
            if ($szCurrencyCode == $icISOCurrency2->getISOCode()) {
                $icISOCurrency = new ISOCurrency(
                    $icISOCurrency2->getISOCode(),
                    $icISOCurrency2->getCurrency(),
                    $icISOCurrency2->getCurrencyShort(),
                    $icISOCurrency2->getExponent()
                );

                //$boFound = true;
                $boFound = $icISOCurrency2->getCurrencyShort();
            }

            ++$nCount;
        }

        return $boFound;
    }

    public function getISOExponent($szCurrencyShort)
    {
        $boFound = false;
        $nCount = 0;
        $icISOCurrency2 = null;

        $icISOCurrency = null;

        while (!$boFound && $nCount < count($this->m_licISOCurrencies)) {
            $icISOCurrency2 = $this->m_licISOCurrencies[$nCount];
            if ($szCurrencyShort == $icISOCurrency2->getISOCode()) {
                $icISOCurrency = new ISOCurrency(
                    $icISOCurrency2->getISOCode(),
                    $icISOCurrency2->getCurrency(),
                    $icISOCurrency2->getCurrencyShort(),
                    $icISOCurrency2->getExponent()
                );
                $boFound = $icISOCurrency2->getExponent();
            }

            ++$nCount;
        }

        return $boFound;
    }

    public function getCurrencySymbol($szCurrencyShort)
    {
        $boFound = false;
        $nCount = 0;
        $icISOCurrency2 = null;

        $icISOCurrency = null;

        while (!$boFound && $nCount < count($this->m_licISOCurrencies)) {
            $icISOCurrency2 = $this->m_licISOCurrencies[$nCount];
            if ($szCurrencyShort == $icISOCurrency2->getISOCode()) {
                $boFound = $icISOCurrency2->getCurrencySymbol();
            }

            ++$nCount;
        }

        return $boFound;
    }

    public function getCount()
    {
        return count($this->m_licISOCurrencies);
    }

    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_licISOCurrencies)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_licISOCurrencies[$nIndex];
    }

    public function add(
        $nISOCode,
        $szCurrency,
        $szCurrencyShort,
        $nExponent,
        $symbolCode = null
    ) {
        $newISOCurrency = new ISOCurrency(
            $nISOCode,
            $szCurrency,
            $szCurrencyShort,
            $nExponent,
            $symbolCode
        );

        $this->m_licISOCurrencies[] = $newISOCurrency;
    }

    //constructor
    public function __construct()
    {
        $this->m_licISOCurrencies = [];
    }
}
