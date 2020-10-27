<?php

namespace App\Utils\TakePayments\Helpers;

class Configuration
{
    private $merchantId;
    private $merchantPassword;
    private $preSharedKey;
    private $secretKey;
    private $integrationType;
    private $hashMethod;
    private $transactionType;
    private $resultDeliveryMethod;
    private $currencyCode;
    private $hostedIframe;
    private $hostedCustomerDetails;
    private $payzoneImages;
    private $orderDetails;

    /**
     * Get the value of merchantId.
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * Set the value of merchantId.
     *
     * @return self
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * Get the value of merchantPassword.
     */
    public function getMerchantPassword()
    {
        return $this->merchantPassword;
    }

    /**
     * Set the value of merchantPassword.
     *
     * @return self
     */
    public function setMerchantPassword($merchantPassword)
    {
        $this->merchantPassword = $merchantPassword;

        return $this;
    }

    /**
     * Get the value of preSharedKey.
     */
    public function getPreSharedKey()
    {
        return $this->preSharedKey;
    }

    /**
     * Set the value of preSharedKey.
     *
     * @return self
     */
    public function setPreSharedKey($preSharedKey)
    {
        $this->preSharedKey = $preSharedKey;

        return $this;
    }

    /**
     * Get the value of secretKey.
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Set the value of secretKey.
     *
     * @return self
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * Get the value of integrationType.
     */
    public function getIntegrationType()
    {
        return $this->integrationType;
    }

    /**
     * Set the value of integrationType.
     *
     * @return self
     */
    public function setIntegrationType($integrationType)
    {
        $this->integrationType = $integrationType;

        return $this;
    }

    /**
     * Get the value of hashMethod.
     */
    public function getHashMethod()
    {
        return $this->hashMethod;
    }

    /**
     * Set the value of hashMethod.
     *
     * @return self
     */
    public function setHashMethod($hashMethod)
    {
        $this->hashMethod = $hashMethod;

        return $this;
    }

    /**
     * Get the value of transactionType.
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set the value of transactionType.
     *
     * @return self
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get the value of resultDeliveryMethod.
     */
    public function getResultDeliveryMethod()
    {
        return $this->resultDeliveryMethod;
    }

    /**
     * Set the value of resultDeliveryMethod.
     *
     * @return self
     */
    public function setResultDeliveryMethod($resultDeliveryMethod)
    {
        $this->resultDeliveryMethod = $resultDeliveryMethod;

        return $this;
    }

    /**
     * Get the value of currencyCode.
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Set the value of currencyCode.
     *
     * @return self
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get the value of hostedIframe.
     */
    public function getHostedIframe()
    {
        return $this->hostedIframe;
    }

    /**
     * Set the value of hostedIframe.
     *
     * @return self
     */
    public function setHostedIframe($hostedIframe)
    {
        $this->hostedIframe = $hostedIframe;

        return $this;
    }

    /**
     * Get the value of hostedCustomerDetails.
     */
    public function getHostedCustomerDetails()
    {
        return $this->hostedCustomerDetails;
    }

    /**
     * Set the value of hostedCustomerDetails.
     *
     * @return self
     */
    public function setHostedCustomerDetails($hostedCustomerDetails)
    {
        $this->hostedCustomerDetails = $hostedCustomerDetails;

        return $this;
    }

    /**
     * Get the value of payzoneImages.
     */
    public function getPayzoneImages()
    {
        return $this->payzoneImages;
    }

    /**
     * Set the value of payzoneImages.
     *
     * @return self
     */
    public function setPayzoneImages($payzoneImages)
    {
        $this->payzoneImages = $payzoneImages;

        return $this;
    }

    /**
     * Get the value of orderDetails.
     */
    public function getOrderDetails()
    {
        return $this->orderDetails;
    }

    /**
     * Set the value of orderDetails.
     *
     * @return self
     */
    public function setOrderDetails($orderDetails)
    {
        $this->orderDetails = $orderDetails;

        return $this;
    }
}
