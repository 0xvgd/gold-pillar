<?php

namespace App\Utils\TakePayments\Gateway\Common;

class ISOCurrency
{
    private $m_nExponent;
    private $m_nISOCode;
    private $m_szCurrency;
    private $m_szCurrencyShort;
    private $m_szSymbolCode;

    //public properties
    public function getExponent()
    {
        return $this->m_nExponent;
    }

    public function getCurrency()
    {
        return $this->m_szCurrency;
    }

    public function getCurrencyShort()
    {
        return $this->m_szCurrencyShort;
    }

    public function getISOCode()
    {
        return $this->m_nISOCode;
    }

    public function getCurrencySymbol()
    {
        return $this->m_szSymbolCode;
    }

    public function getAmountCurrencyString(
        $nAmount,
        $boAppendCurrencyShort = true
    ) {
        $szReturnString = '';

        $nDivideAmount = pow(10, $this->m_nExponent);
        $lfAmount = $nAmount / $nDivideAmount;

        $szFormatString = '%.'.$this->m_nExponent.'f';
        $szReturnString = sprintf($szFormatString, $lfAmount);

        if ($boAppendCurrencyShort) {
            $szReturnString = $szReturnString.' '.$this->m_szCurrencyShort;
        }

        return $szReturnString;
    }

    //constructor
    public function __construct(
        $nISOCode,
        $szCurrency,
        $szCurrencyShort,
        $nExponent,
        $symbolCode = null
    ) {
        $this->m_nISOCode = $nISOCode;
        $this->m_nExponent = $nExponent;
        $this->m_szCurrency = $szCurrency;
        $this->m_szCurrencyShort = $szCurrencyShort;
        $this->m_szSymbolCode = $symbolCode;
    }
}
