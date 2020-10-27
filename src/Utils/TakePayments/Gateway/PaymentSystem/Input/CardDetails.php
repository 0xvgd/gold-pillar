<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

class CardDetails
{
    /**
     * @var string
     */
    private $m_szCardName;
    /**
     * @var string
     */
    private $m_szCardNumber;
    /**
     * @var ExpiryDate
     */
    private $m_edExpiryDate;
    /**
     * @var StartDate
     */
    private $m_sdStartDate;
    /**
     * @var string
     */
    private $m_szIssueNumber;
    /**
     * @var string
     */
    private $m_szCV2;

    /**
     * @return string
     */
    public function getCardName()
    {
        return $this->m_szCardName;
    }

    /**
     * @param string $cardName
     */
    public function setCardName($cardName)
    {
        $this->m_szCardName = $cardName;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->m_szCardNumber;
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber($cardNumber)
    {
        $this->m_szCardNumber = $cardNumber;
    }

    /**
     * @return ExpiryDate
     */
    public function getExpiryDate()
    {
        return $this->m_edExpiryDate;
    }

    /**
     * @return StartDate
     */
    public function getStartDate()
    {
        return $this->m_sdStartDate;
    }

    /**
     * @return string
     */
    public function getIssueNumber()
    {
        return $this->m_szIssueNumber;
    }

    /**
     * @param string $issueNumber
     */
    public function setIssueNumber($issueNumber)
    {
        $this->m_szIssueNumber = $issueNumber;
    }

    /**
     * @return string
     */
    public function getCV2()
    {
        return $this->m_szCV2;
    }

    /**
     * @param string $cv2
     */
    public function setCV2($cv2)
    {
        $this->m_szCV2 = $cv2;
    }

    //constructor
    public function __construct()
    {
        $this->m_szCardName = '';
        $this->m_szCardNumber = '';
        $this->m_edExpiryDate = new ExpiryDate();
        $this->m_sdStartDate = new StartDate();
        $this->m_szIssueNumber = '';
        $this->m_szCV2 = '';
    }
}
