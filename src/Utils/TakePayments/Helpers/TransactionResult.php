<?php

namespace App\Utils\TakePayments\Helpers;

class TransactionResult
{
    private $m_nStatusCode;
    private $m_Message;
    private $m_nPreviousStatusCode;
    private $m_PreviousMessage;
    private $m_CrossReference;
    private $m_AddressNumericCheckResult;
    private $m_PostCodeCheckResult;
    private $m_CV2CheckResult;
    private $m_ThreeDSecureAuthenticationCheckResult;
    private $m_CardType;
    private $m_CardClass;
    private $m_CardIssuer;
    private $m_nCardIssuerCountryCode;
    private $m_nAmount;
    private $m_nCurrencyCode;
    private $m_OrderID;
    private $m_TransactionType;
    private $m_TransactionDateTime;
    private $m_OrderDescription;
    private $m_CustomerName;
    private $m_Address1;
    private $m_Address2;
    private $m_Address3;
    private $m_Address4;
    private $m_City;
    private $m_State;
    private $m_PostCode;
    private $m_nCountryCode;
    private $m_EmailAddress;
    private $m_PhoneNumber;

    private $m_szMerchantID;
    private $m_szACSURL;
    private $m_szPaREQ;
    private $m_szCallbackUrl;
    private $m_szPaRES;

    public function getStatusCode()
    {
        return $this->m_nStatusCode;
    }

    public function setStatusCode($nStatusCode)
    {
        $this->m_nStatusCode = $nStatusCode;
    }

    public function getMessage()
    {
        return $this->m_Message;
    }

    public function setMessage($Message)
    {
        $this->m_Message = $Message;
    }

    public function getPreviousStatusCode()
    {
        return $this->m_nPreviousStatusCode;
    }

    public function setPreviousStatusCode($nPreviousStatusCode)
    {
        $this->m_nPreviousStatusCode = $nPreviousStatusCode;
    }

    public function getPreviousMessage()
    {
        return $this->m_PreviousMessage;
    }

    public function setPreviousMessage($PreviousMessage)
    {
        $this->m_PreviousMessage = $PreviousMessage;
    }

    public function getCrossReference()
    {
        return $this->m_CrossReference;
    }

    public function setCrossReference($CrossReference)
    {
        $this->m_CrossReference = $CrossReference;
    }

    public function getAddressNumericCheckResult()
    {
        return $this->m_AddressNumericCheckResult;
    }

    public function setAddressNumericCheckResult($AddressNumericCheckResult)
    {
        $this->m_AddressNumericCheckResult = $AddressNumericCheckResult;
    }

    public function getPostCodeCheckResult()
    {
        return $this->m_PostCodeCheckResult;
    }

    public function setPostCodeCheckResult($PostCodeCheckResult)
    {
        $this->m_PostCodeCheckResult = $PostCodeCheckResult;
    }

    public function getCV2CheckResult()
    {
        return $this->m_CV2CheckResult;
    }

    public function setCV2CheckResult($CV2CheckResult)
    {
        $this->m_CV2CheckResult = $CV2CheckResult;
    }

    public function getThreeDSecureAuthenticationCheckResult()
    {
        return $this->m_ThreeDSecureAuthenticationCheckResult;
    }

    public function setThreeDSecureAuthenticationCheckResult(
        $ThreeDSecureAuthenticationCheckResult
    ) {
        $this->m_ThreeDSecureAuthenticationCheckResult = $ThreeDSecureAuthenticationCheckResult;
    }

    public function getCardType()
    {
        return $this->m_CardType;
    }

    public function setCardType($CardType)
    {
        $this->m_CardType = $CardType;
    }

    public function getCardClass()
    {
        return $this->m_CardClass;
    }

    public function setCardClass($CardClass)
    {
        $this->m_CardClass = $CardClass;
    }

    public function getCardIssuer()
    {
        return $this->m_CardIssuer;
    }

    public function setCardIssuer($CardIssuer)
    {
        $this->m_CardIssuer = $CardIssuer;
    }

    public function getCardIssuerCountryCode()
    {
        return $this->m_nCardIssuerCountryCode;
    }

    public function setCardIssuerCountryCode($nCardIssuerCountryCode)
    {
        $this->m_nCardIssuerCountryCode = $nCardIssuerCountryCode;
    }

    public function getAmount()
    {
        return $this->m_nAmount;
    }

    public function setAmount($nAmount)
    {
        $this->m_nAmount = $nAmount;
    }

    public function getCurrencyCode()
    {
        return $this->m_nCurrencyCode;
    }

    public function setCurrencyCode($nCurrencyCode)
    {
        $this->m_nCurrencyCode = $nCurrencyCode;
    }

    public function getOrderID()
    {
        return $this->m_OrderID;
    }

    public function setOrderID($OrderID)
    {
        $this->m_OrderID = $OrderID;
    }

    public function getTransactionType()
    {
        return $this->m_TransactionType;
    }

    public function setTransactionType($TransactionType)
    {
        $this->m_TransactionType = $TransactionType;
    }

    public function getTransactionDateTime()
    {
        return $this->m_TransactionDateTime;
    }

    public function setTransactionDateTime($TransactionDateTime)
    {
        $this->m_TransactionDateTime = $TransactionDateTime;
    }

    public function getOrderDescription()
    {
        return $this->m_OrderDescription;
    }

    public function setOrderDescription($OrderDescription)
    {
        $this->m_OrderDescription = $OrderDescription;
    }

    public function getCustomerName()
    {
        return $this->m_CustomerName;
    }

    public function setCustomerName($CustomerName)
    {
        $this->m_CustomerName = $CustomerName;
    }

    public function getAddress1()
    {
        return $this->m_Address1;
    }

    public function setAddress1($Address1)
    {
        $this->m_Address1 = $Address1;
    }

    public function getAddress2()
    {
        return $this->m_Address2;
    }

    public function setAddress2($Address2)
    {
        $this->m_Address2 = $Address2;
    }

    public function getAddress3()
    {
        return $this->m_Address3;
    }

    public function setAddress3($Address3)
    {
        $this->m_Address3 = $Address3;
    }

    public function getAddress4()
    {
        return $this->m_Address4;
    }

    public function setAddress4($Address4)
    {
        $this->m_Address4 = $Address4;
    }

    public function getCity()
    {
        return $this->m_City;
    }

    public function setCity($City)
    {
        $this->m_City = $City;
    }

    public function getState()
    {
        return $this->m_State;
    }

    public function setState($State)
    {
        $this->m_State = $State;
    }

    public function getPostCode()
    {
        return $this->m_PostCode;
    }

    public function setPostCode($PostCode)
    {
        $this->m_PostCode = $PostCode;
    }

    public function getCountryCode()
    {
        return $this->m_nCountryCode;
    }

    public function setCountryCode($nCountryCode)
    {
        $this->m_nCountryCode = $nCountryCode;
    }

    public function getEmailAddress()
    {
        return $this->m_EmailAddress;
    }

    public function setEmailAddress($emailAddress)
    {
        $this->m_EmailAddress = $emailAddress;
    }

    public function getPhoneNumber()
    {
        return $this->m_PhoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->m_PhoneNumber = $phoneNumber;
    }

    public function setMerchantID($MerchantID)
    {
        $this->m_szMerchantID = $MerchantID;
    }

    public function getMerchantID()
    {
        return $this->m_szMerchantID;
    }

    public function setACSURL($ACSURL)
    {
        $this->m_szACSURL = $ACSURL;
    }

    public function getACSURL()
    {
        return $this->m_szACSURL;
    }

    public function setPaREQ($PaREQ)
    {
        $this->m_szPaREQ = $PaREQ;
    }

    public function getPaREQ()
    {
        return $this->m_szPaREQ;
    }

    public function setCallbackUrl($CallbackUrl)
    {
        $this->m_szCallbackUrl = $CallbackUrl;
    }

    public function getCallbackURL()
    {
        return $this->m_szCallbackUrl;
    }

    public function setPaRES($PaRES)
    {
        $this->m_szPaRES = $PaRES;
    }

    public function getPaRES()
    {
        return $this->m_szPaRES;
    }
}
