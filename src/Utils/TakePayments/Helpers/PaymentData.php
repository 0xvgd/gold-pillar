<?php

namespace App\Utils\TakePayments\Helpers;

class PaymentData
{
    private $orderId;
    private $country;
    private $fullAmount;
    private $transactionDateTime;
    private $orderDescription;
    private $customerName;
    private $address1;
    private $address2;
    private $address3;
    private $address4;
    private $city;
    private $state;
    private $postCode;
    private $emailAddress;
    private $cardName;
    private $cardNumber;
    private $CV2;
    private $issueNumber;
    private $expiryDateMonth;
    private $expiryDateYear;
    private $startDateMonth;
    private $startDateYear;
    private $crossReferenceTransaction;
    private $crossReference;
    private $paRes;
    private $md;

    /**
     * Get the value of orderId.
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set the value of orderId.
     *
     * @return self
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get the value of country.
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country.
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of fullAmount.
     */
    public function getFullAmount()
    {
        return $this->fullAmount;
    }

    /**
     * Set the value of fullAmount.
     *
     * @return self
     */
    public function setFullAmount($fullAmount)
    {
        $this->fullAmount = $fullAmount;

        return $this;
    }

    /**
     * Get the value of transactionDateTime.
     */
    public function getTransactionDateTime()
    {
        return $this->transactionDateTime;
    }

    /**
     * Set the value of transactionDateTime.
     *
     * @return self
     */
    public function setTransactionDateTime($transactionDateTime)
    {
        $this->transactionDateTime = $transactionDateTime;

        return $this;
    }

    /**
     * Get the value of orderDescription.
     */
    public function getOrderDescription()
    {
        return $this->orderDescription;
    }

    /**
     * Set the value of orderDescription.
     *
     * @return self
     */
    public function setOrderDescription($orderDescription)
    {
        $this->orderDescription = $orderDescription;

        return $this;
    }

    /**
     * Get the value of customerName.
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * Set the value of customerName.
     *
     * @return self
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * Get the value of address1.
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set the value of address1.
     *
     * @return self
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get the value of address2.
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set the value of address2.
     *
     * @return self
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get the value of address3.
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set the value of address3.
     *
     * @return self
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;

        return $this;
    }

    /**
     * Get the value of address4.
     */
    public function getAddress4()
    {
        return $this->address4;
    }

    /**
     * Set the value of address4.
     *
     * @return self
     */
    public function setAddress4($address4)
    {
        $this->address4 = $address4;

        return $this;
    }

    /**
     * Get the value of city.
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city.
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of state.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the value of state.
     *
     * @return self
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get the value of postCode.
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set the value of postCode.
     *
     * @return self
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get the value of emailAddress.
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set the value of emailAddress.
     *
     * @return self
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get the value of cardName.
     */
    public function getCardName()
    {
        return $this->cardName;
    }

    /**
     * Set the value of cardName.
     *
     * @return self
     */
    public function setCardName($cardName)
    {
        $this->cardName = $cardName;

        return $this;
    }

    /**
     * Get the value of cardNumber.
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set the value of cardNumber.
     *
     * @return self
     */
    public function setCardNumber($cardNumber)
    {
        $withoutSpaces = str_replace(' ', '', $cardNumber);
        $this->cardNumber = $withoutSpaces;

        return $this;
    }

    /**
     * Get the value of CV2.
     */
    public function getCV2()
    {
        return $this->CV2;
    }

    /**
     * Set the value of CV2.
     *
     * @return self
     */
    public function setCV2($CV2)
    {
        $this->CV2 = $CV2;

        return $this;
    }

    /**
     * Get the value of issueNumber.
     */
    public function getIssueNumber()
    {
        return $this->issueNumber;
    }

    /**
     * Set the value of issueNumber.
     *
     * @return self
     */
    public function setIssueNumber($issueNumber)
    {
        $this->issueNumber = $issueNumber;

        return $this;
    }

    /**
     * Get the value of expiryDateMonth.
     */
    public function getExpiryDateMonth()
    {
        return $this->expiryDateMonth;
    }

    /**
     * Set the value of expiryDateMonth.
     *
     * @return self
     */
    public function setExpiryDateMonth($expiryDateMonth)
    {
        $this->expiryDateMonth = $expiryDateMonth;

        return $this;
    }

    /**
     * Get the value of expiryDateYear.
     */
    public function getExpiryDateYear()
    {
        return $this->expiryDateYear;
    }

    /**
     * Set the value of expiryDateYear.
     *
     * @return self
     */
    public function setExpiryDateYear($expiryDateYear)
    {
        $this->expiryDateYear = $expiryDateYear;

        return $this;
    }

    /**
     * Get the value of startDateMonth.
     */
    public function getStartDateMonth()
    {
        return $this->startDateMonth;
    }

    /**
     * Set the value of startDateMonth.
     *
     * @return self
     */
    public function setStartDateMonth($startDateMonth)
    {
        $this->startDateMonth = $startDateMonth;

        return $this;
    }

    /**
     * Get the value of startDateYear.
     */
    public function getStartDateYear()
    {
        return $this->startDateYear;
    }

    /**
     * Set the value of startDateYear.
     *
     * @return self
     */
    public function setStartDateYear($startDateYear)
    {
        $this->startDateYear = $startDateYear;

        return $this;
    }

    /**
     * Get the value of crossReferenceTransaction.
     */
    public function getCrossReferenceTransaction()
    {
        return $this->crossReferenceTransaction;
    }

    /**
     * Set the value of crossReferenceTransaction.
     *
     * @return self
     */
    public function setCrossReferenceTransaction($crossReferenceTransaction)
    {
        $this->crossReferenceTransaction = $crossReferenceTransaction;

        return $this;
    }

    /**
     * Get the value of crossReference.
     */
    public function getCrossReference()
    {
        return $this->crossReference;
    }

    /**
     * Set the value of crossReference.
     *
     * @return self
     */
    public function setCrossReference($crossReference)
    {
        $this->crossReference = $crossReference;

        return $this;
    }

    /**
     * Get the value of paRes.
     */
    public function getPaRes()
    {
        return $this->paRes;
    }

    /**
     * Set the value of paRes.
     *
     * @return self
     */
    public function setPaRes($paRes)
    {
        $this->paRes = $paRes;

        return $this;
    }

    /**
     * Get the value of md.
     */
    public function getMd()
    {
        return $this->md;
    }

    /**
     * Set the value of md.
     *
     * @return self
     */
    public function setMd($md)
    {
        $this->md = $md;

        return $this;
    }
}
