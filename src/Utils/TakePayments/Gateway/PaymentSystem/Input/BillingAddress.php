<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use App\Utils\TakePayments\Gateway\Common\NullableInt;

class BillingAddress
{
    /**
     * @var string
     */
    private $m_szAddress1;
    /**
     * @var string
     */
    private $m_szAddress2;
    /**
     * @var string
     */
    private $m_szAddress3;
    /**
     * @var string
     */
    private $m_szAddress4;
    /**
     * @var string
     */
    private $m_szCity;
    /**
     * @var string
     */
    private $m_szState;
    /**
     * @var string
     */
    private $m_szPostCode;
    /**
     * @var NullableInt
     */
    private $m_nCountryCode;

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->m_szAddress1;
    }

    /**
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->m_szAddress1 = $address1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->m_szAddress2;
    }

    /**
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->m_szAddress2 = $address2;
    }

    /**
     * @return string
     */
    public function getAddress3()
    {
        return $this->m_szAddress3;
    }

    /**
     * @param string $address3
     */
    public function setAddress3($address3)
    {
        $this->m_szAddress3 = $address3;
    }

    /**
     * @return string
     */
    public function getAddress4()
    {
        return $this->m_szAddress4;
    }

    /**
     * @param string $address4
     */
    public function setAddress4($address4)
    {
        $this->m_szAddress4 = $address4;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->m_szCity;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->m_szCity = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->m_szState;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->m_szState = $state;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->m_szPostCode;
    }

    /**
     * @param string $postCode
     */
    public function setPostCode($postCode)
    {
        $this->m_szPostCode = $postCode;
    }

    /**
     * @return NullableInt
     */
    public function getCountryCode()
    {
        return $this->m_nCountryCode;
    }

    //constructor
    public function __construct()
    {
        $this->m_szAddress1 = '';
        $this->m_szAddress2 = '';
        $this->m_szAddress3 = '';
        $this->m_szAddress4 = '';
        $this->m_szCity = '';
        $this->m_szState = '';
        $this->m_szPostCode = '';
        $this->m_nCountryCode = new NullableInt();
    }
}
