<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use App\Utils\TakePayments\Gateway\Common\NullableBool;
use App\Utils\TakePayments\Gateway\Common\NullableInt;

class TransactionDetails
{
    /**
     * @var MessageDetails
     */
    private $m_mdMessageDetails;
    /**
     * @var NullableInt
     */
    private $m_nAmount;
    /**
     * @var NullableInt
     */
    private $m_nCurrencyCode;
    /**
     * @var string
     */
    private $m_szCaptureEnvironment;
    /**
     * @var NullableBool
     */
    private $m_boContinuousAuthority;
    /**
     * @var string
     */
    private $m_szOrderID;
    /**
     * @var string
     */
    private $m_szOrderDescription;
    /**
     * @var TransactionControl
     */
    private $m_tcTransactionControl;
    /**
     * @var ThreeDSecureBrowserDetails
     */
    private $m_tdsbdThreeDSecureBrowserDetails;

    /**
     * @return MessageDetails
     */
    public function getMessageDetails()
    {
        return $this->m_mdMessageDetails;
    }

    /**
     * @return NullableInt
     */
    public function getAmount()
    {
        return $this->m_nAmount;
    }

    /**
     * @return NullableInt
     */
    public function getCurrencyCode()
    {
        return $this->m_nCurrencyCode;
    }

    /**
     * @return string
     */
    public function getCaptureEnvironment()
    {
        return $this->m_szCaptureEnvironment;
    }

    /**
     * @param string $szCaptureEnvironment
     */
    public function setCaptureEnvironment($szCaptureEnvironment)
    {
        $this->m_szCaptureEnvironment = $szCaptureEnvironment;
    }

    /**
     * @return NullableBool
     */
    public function getContinuousAuthority()
    {
        return $this->m_boContinuousAuthority;
    }

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->m_szOrderID;
    }

    /**
     * @param string $orderID
     */
    public function setOrderID($orderID)
    {
        $this->m_szOrderID = $orderID;
    }

    /**
     * @return string
     */
    public function getOrderDescription()
    {
        return $this->m_szOrderDescription;
    }

    /**
     * @param string $orderDescription
     */
    public function setOrderDescription($orderDescription)
    {
        $this->m_szOrderDescription = $orderDescription;
    }

    /**
     * @return TransactionControl
     */
    public function getTransactionControl()
    {
        return $this->m_tcTransactionControl;
    }

    /**
     * @return ThreeDSecureBrowserDetails
     */
    public function getThreeDSecureBrowserDetails()
    {
        return $this->m_tdsbdThreeDSecureBrowserDetails;
    }

    //constructor
    public function __construct()
    {
        $this->m_mdMessageDetails = new MessageDetails();
        $this->m_nAmount = new NullableInt();
        $this->m_szCaptureEnvironment = '';
        $this->m_boContinuousAuthority = new NullableBool();
        $this->m_nCurrencyCode = new NullableInt();
        $this->m_szOrderID = '';
        $this->m_szOrderDescription = '';
        $this->m_tcTransactionControl = new TransactionControl();
        $this->m_tdsbdThreeDSecureBrowserDetails = new ThreeDSecureBrowserDetails();
    }
}
