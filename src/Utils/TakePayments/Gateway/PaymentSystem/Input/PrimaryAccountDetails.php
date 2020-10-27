<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

class PrimaryAccountDetails
{
    /**
     * @var AddressDetails
     */
    private $m_adAddressDetails;
    /**
     * @var string
     */
    private $m_szName;
    /**
     * @var string
     */
    private $m_szAccountNumber;
    /**
     * @var string
     */
    private $m_szDateOfBirth;

    /**
     * @return AddressDetails
     */
    public function getAddressDetails()
    {
        return $this->m_adAddressDetails;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->m_szName;
    }

    /**
     * @param string $m_szName
     */
    public function setName($m_szName)
    {
        $this->m_szName = $m_szName;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->m_szAccountNumber;
    }

    /**
     * @param string $m_szAccountNumber
     */
    public function setAccountNumber($m_szAccountNumber)
    {
        $this->m_szAccountNumber = $m_szAccountNumber;
    }

    /**
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->m_szDateOfBirth;
    }

    /**
     * @param string $m_szDateOfBirth
     */
    public function setDateOfBirth($m_szDateOfBirth)
    {
        $this->m_szDateOfBirth = $m_szDateOfBirth;
    }

    //constructor
    public function __construct()
    {
        $this->m_adAddressDetails = new AddressDetails();
        $this->m_szName = '';
        $this->m_szAccountNumber = '';
        $this->m_szDateOfBirth = '';
    }
}
