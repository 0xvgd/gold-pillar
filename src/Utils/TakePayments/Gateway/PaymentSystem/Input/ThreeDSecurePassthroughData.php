<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

class ThreeDSecurePassthroughData
{
    /**
     * @var string
     */
    private $m_szEnrolmentStatus;
    /**
     * @var string
     */
    private $m_szAuthenticationStatus;
    /**
     * @var string
     */
    private $m_szElectronicCommerceIndicator;
    /**
     * @var string
     */
    private $m_szAuthenticationValue;
    /**
     * @var string
     */
    private $m_szTransactionIdentifier;

    /**
     * @return string
     */
    public function getEnrolmentStatus()
    {
        return $this->m_szEnrolmentStatus;
    }

    /**
     * @param string $enrolmentStatus
     */
    public function setEnrolmentStatus($enrolmentStatus)
    {
        $this->m_szEnrolmentStatus = $enrolmentStatus;
    }

    /**
     * @return string
     */
    public function getAuthenticationStatus()
    {
        return $this->m_szAuthenticationStatus;
    }

    /**
     * @param string $authenticationStatus
     */
    public function setAuthenticationStatus($authenticationStatus)
    {
        $this->m_szAuthenticationStatus = $authenticationStatus;
    }

    /**
     * @return string
     */
    public function getElectronicCommerceIndicator()
    {
        return $this->m_szElectronicCommerceIndicator;
    }

    /**
     * @param string $electronicCommerceIndicator
     */
    public function setElectronicCommerceIndicator($electronicCommerceIndicator)
    {
        $this->m_szElectronicCommerceIndicator = $electronicCommerceIndicator;
    }

    /**
     * @return string
     */
    public function getAuthenticationValue()
    {
        return $this->m_szAuthenticationValue;
    }

    /**
     * @param string $authenticationValue
     */
    public function setAuthenticationValue($authenticationValue)
    {
        $this->m_szAuthenticationValue = $authenticationValue;
    }

    /**
     * @return string
     */
    public function getTransactionIdentifier()
    {
        return $this->m_szTransactionIdentifier;
    }

    /**
     * @param string $transactionIdentifier
     */
    public function setTransactionIdentifier($transactionIdentifier)
    {
        $this->m_szTransactionIdentifier = $transactionIdentifier;
    }

    //constructor
    public function __construct()
    {
        $this->m_szEnrolmentStatus = '';
        $this->m_szAuthenticationStatus = '';
        $this->m_szElectronicCommerceIndicator = '';
        $this->m_szAuthenticationValue = '';
        $this->m_szTransactionIdentifier = '';
    }
}
