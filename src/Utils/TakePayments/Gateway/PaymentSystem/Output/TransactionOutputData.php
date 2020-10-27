<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\GenericVariableList;

class TransactionOutputData extends BaseOutputData
{
    /**
     * @var string
     */
    private $m_szCrossReference;
    /**
     * @var string
     */
    private $m_szAuthCode;
    /**
     * @var string
     */
    private $m_szAddressNumericCheckResult;
    /**
     * @var string
     */
    private $m_szPostCodeCheckResult;
    /**
     * @var string
     */
    private $m_szThreeDSecureAuthenticationCheckResult;
    /**
     * @var string
     */
    private $m_szCV2CheckResult;
    /**
     * @var CardTypeData
     */
    private $m_ctdCardTypeData;
    /**
     * @var NullableInt
     */
    private $m_nAmountReceived;
    /**
     * @var ThreeDSecureOutputData
     */
    private $m_tdsodThreeDSecureOutputData;
    /**
     * @var GenericVariableList
     */
    private $m_lgvCustomVariables;
    /**
     * @var GatewayEntryPointList
     */
    private $m_gepodGatewayEntryPointsOutputData;

    /**
     * @return string
     */
    public function getCrossReference()
    {
        return $this->m_szCrossReference;
    }

    /**
     * @return string
     */
    public function getAuthCode()
    {
        return $this->m_szAuthCode;
    }

    /**
     * @return string
     */
    public function getAddressNumericCheckResult()
    {
        return $this->m_szAddressNumericCheckResult;
    }

    /**
     * @return string
     */
    public function getPostCodeCheckResult()
    {
        return $this->m_szPostCodeCheckResult;
    }

    /**
     * @return string
     */
    public function getThreeDSecureAuthenticationCheckResult()
    {
        return $this->m_szThreeDSecureAuthenticationCheckResult;
    }

    /**
     * @return string
     */
    public function getCV2CheckResult()
    {
        return $this->m_szCV2CheckResult;
    }

    /**
     * @return CardTypeData
     */
    public function getCardTypeData()
    {
        return $this->m_ctdCardTypeData;
    }

    /**
     * @return NullableInt
     */
    public function getAmountReceived()
    {
        return $this->m_nAmountReceived;
    }

    /**
     * @return ThreeDSecureOutputData
     */
    public function getThreeDSecureOutputData()
    {
        return $this->m_tdsodThreeDSecureOutputData;
    }

    /**
     * @return GenericVariableList
     */
    public function getCustomVariables()
    {
        return $this->m_lgvCustomVariables;
    }

    /**
     * @return GatewayEntryPointList
     */
    public function getGatewayEntryPoints()
    {
        return $this->m_gepodGatewayEntryPointsOutputData;
    }

    //constructor

    /**
     * @param GatewayEntryPointList  $szCrossReference
     * @param string                 $szAuthCode
     * @param string                 $szAddressNumericCheckResult
     * @param string                 $szPostCodeCheckResult
     * @param string                 $szThreeDSecureAuthenticationCheckResult
     * @param string                 $szCV2CheckResult
     * @param CardTypeData           $ctdCardTypeData
     * @param NullableInt            $nAmountReceived
     * @param ThreeDSecureOutputData $tdsodThreeDSecureOutputData
     * @param GenericVariableList    $lgvCustomVariables
     * @param GatewayEntryPointList  $lgepGatewayEntryPoints
     */
    public function __construct(
        $szCrossReference,
        $szAuthCode,
        $szAddressNumericCheckResult,
        $szPostCodeCheckResult,
        $szThreeDSecureAuthenticationCheckResult,
        $szCV2CheckResult,
        CardTypeData $ctdCardTypeData = null,
        NullableInt $nAmountReceived = null,
        ThreeDSecureOutputData $tdsodThreeDSecureOutputData = null,
        GenericVariableList $lgvCustomVariables = null,
        GatewayEntryPointList $lgepGatewayEntryPoints = null
    ) {
        //first calling the parent constructor
        parent::__construct($lgepGatewayEntryPoints);

        $this->m_szCrossReference = $szCrossReference;
        $this->m_szAuthCode = $szAuthCode;
        $this->m_szAddressNumericCheckResult = $szAddressNumericCheckResult;
        $this->m_szPostCodeCheckResult = $szPostCodeCheckResult;
        $this->m_szThreeDSecureAuthenticationCheckResult = $szThreeDSecureAuthenticationCheckResult;
        $this->m_szCV2CheckResult = $szCV2CheckResult;
        $this->m_ctdCardTypeData = $ctdCardTypeData;
        $this->m_nAmountReceived = $nAmountReceived;
        $this->m_tdsodThreeDSecureOutputData = $tdsodThreeDSecureOutputData;
        $this->m_lgvCustomVariables = $lgvCustomVariables;
        $this->m_gepodGatewayEntryPointsOutputData = $lgepGatewayEntryPoints;
    }
}
