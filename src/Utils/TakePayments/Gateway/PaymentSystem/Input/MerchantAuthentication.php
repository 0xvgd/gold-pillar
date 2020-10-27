<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

class MerchantAuthentication
{
    /**
     * @var string
     */
    private $m_szMerchantID;
    /**
     * @var string
     */
    private $m_szPassword;

    /**
     * @return string
     */
    public function getMerchantID()
    {
        return $this->m_szMerchantID;
    }

    /**
     * @param string $merchantID
     */
    public function setMerchantID($merchantID)
    {
        $this->m_szMerchantID = $merchantID;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->m_szPassword;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->m_szPassword = $password;
    }

    //constructor
    public function __construct()
    {
        $this->m_szMerchantID = '';
        $this->m_szPassword = '';
    }
}
