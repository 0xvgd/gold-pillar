<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

class CustomerDetails
{
    /**
     * @var BillingAddress
     */
    private $m_adBillingAddress;
    /**
     * @var PrimaryAccountDetails
     */
    private $m_padPrimaryAccountDetails;
    /**
     * @var string
     */
    private $m_szEmailAddress;
    /**
     * @var string
     */
    private $m_szPhoneNumber;
    /**
     * @var string
     */
    private $m_szCustomerIPAddress;

    /**
     * @return BillingAddress
     */
    public function getBillingAddress()
    {
        return $this->m_adBillingAddress;
    }

    /**
     * @return PrimaryAccountDetails
     */
    public function getPrimaryAccountDetails()
    {
        return $this->m_padPrimaryAccountDetails;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->m_szEmailAddress;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->m_szEmailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->m_szPhoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->m_szPhoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getCustomerIPAddress()
    {
        return $this->m_szCustomerIPAddress;
    }

    /**
     * @param string $IPAddress
     */
    public function setCustomerIPAddress($IPAddress)
    {
        $this->m_szCustomerIPAddress = $IPAddress;
    }

    //constructor
    public function __construct()
    {
        $this->m_adBillingAddress = new BillingAddress();
        $this->m_padPrimaryAccountDetails = new PrimaryAccountDetails();
        $this->m_szEmailAddress = '';
        $this->m_szPhoneNumber = '';
        $this->m_szCustomerIPAddress = '';
    }
}
