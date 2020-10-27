<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use App\Utils\TakePayments\Gateway\Common\NullableBool;

class MessageDetails
{
    /**
     * @var string
     */
    private $m_szTransactionType;
    /**
     * @var NullableBool
     */
    private $m_boNewTransaction;
    /**
     * @var string
     */
    private $m_szCrossReference;
    /**
     * @var string
     */
    private $m_szClientReference;
    /**
     * @var string
     */
    private $m_szPreviousClientReference;

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->m_szTransactionType;
    }

    /**
     * @param string $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->m_szTransactionType = $transactionType;
    }

    /**
     * @return NullableBool
     */
    public function getNewTransaction()
    {
        return $this->m_boNewTransaction;
    }

    /**
     * @return string
     */
    public function getCrossReference()
    {
        return $this->m_szCrossReference;
    }

    /**
     * @param string $crossReference
     */
    public function setCrossReference($crossReference)
    {
        $this->m_szCrossReference = $crossReference;
    }

    /**
     * @return string
     */
    public function getClientReference()
    {
        return $this->m_szClientReference;
    }

    /**
     * @param string $clientReference
     */
    public function setClientReference($clientReference)
    {
        $this->m_szClientReference = $clientReference;
    }

    /**
     * @return string
     */
    public function getPreviousClientReference()
    {
        return $this->m_szPreviousClientReference;
    }

    /**
     * @param string $previousClientReference
     */
    public function setPreviousClientReference($previousClientReference)
    {
        $this->m_szPreviousClientReference = $previousClientReference;
    }

    //constructor
    public function __construct()
    {
        $this->m_szTransactionType = '';
        $this->m_szCrossReference = '';
        $this->m_boNewTransaction = new NullableBool();
        $this->m_szClientReference = '';
        $this->m_szPreviousClientReference = '';
    }
}
