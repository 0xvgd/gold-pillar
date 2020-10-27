<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\NullableBool;

class CardTypeData
{
    /**
     * @var string
     */
    private $m_szCardType;
    /**
     * @var Issuer
     */
    private $m_iIssuer;
    /**
     * @var string
     */
    private $m_szCardClass;
    /**
     * @var NullableBool
     */
    private $m_boLuhnCheckRequired;
    /**
     * @var string
     */
    private $m_szIssueNumberStatus;
    /**
     * @var string
     */
    private $m_szStartDateStatus;

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->m_szCardType;
    }

    /**
     * @return string
     */
    public function getCardClass()
    {
        return $this->m_szCardClass;
    }

    /**
     * @return Issuer
     */
    public function getIssuer()
    {
        return $this->m_iIssuer;
    }

    /**
     * @return NullableBool
     */
    public function getLuhnCheckRequired()
    {
        return $this->m_boLuhnCheckRequired;
    }

    /**
     * @return string
     */
    public function getIssueNumberStatus()
    {
        return $this->m_szIssueNumberStatus;
    }

    /**
     * @return string
     */
    public function getStartDateStatus()
    {
        return $this->m_szStartDateStatus;
    }

    //constructor

    /**
     * @param string       $szCardType
     * @param string       $szCardClass
     * @param string       $iIssuer
     * @param NullableBool $boLuhnCheckRequired
     * @param string       $szIssueNumberStatus
     * @param string       $szStartDateStatus
     */
    public function __construct(
        $szCardType,
        $szCardClass,
        $iIssuer,
        NullableBool $boLuhnCheckRequired = null,
        $szIssueNumberStatus,
        $szStartDateStatus
    ) {
        $this->m_szCardType = $szCardType;
        $this->m_szCardClass = $szCardClass;
        $this->m_iIssuer = $iIssuer;
        $this->m_boLuhnCheckRequired = $boLuhnCheckRequired;
        $this->m_szIssueNumberStatus = $szIssueNumberStatus;
        $this->m_szStartDateStatus = $szStartDateStatus;
    }
}
