<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use App\Utils\TakePayments\Gateway\Common\NullableBool;
use App\Utils\TakePayments\Gateway\Common\NullableInt;

class TransactionControl
{
    /**
     * @var NullableBool
     */
    private $m_boEchoCardType;
    /**
     * @var NullableBool
     */
    private $m_boEchoAVSCheckResult;
    /**
     * @var NullableBool
     */
    private $m_boEchoCV2CheckResult;
    /**
     * @var NullableBool
     */
    private $m_boEchoAmountReceived;
    /**
     * @var NullableInt
     */
    private $m_nDuplicateDelay;
    /**
     * @var string
     */
    private $m_szAVSOverridePolicy;
    /**
     * @var string
     */
    private $m_szCV2OverridePolicy;
    /**
     * @var NullableBool
     */
    private $m_boThreeDSecureOverridePolicy;
    /**
     * @var string
     */
    private $m_szAuthCode;
    /**
     * @var ThreeDSecurePassthroughData
     */
    private $m_tdsptThreeDSecurePassthroughData;
    /**
     * @var GenericVariableList
     */
    private $m_lgvCustomVariables;

    /**
     * @return NullableBool
     */
    public function getEchoCardType()
    {
        return $this->m_boEchoCardType;
    }

    /**
     * @return NullableBool
     */
    public function getEchoAVSCheckResult()
    {
        return $this->m_boEchoAVSCheckResult;
    }

    /**
     * @return NullableBool
     */
    public function getEchoCV2CheckResult()
    {
        return $this->m_boEchoCV2CheckResult;
    }

    /**
     * @return NullableBool
     */
    public function getEchoAmountReceived()
    {
        return $this->m_boEchoAmountReceived;
    }

    /**
     * @return NullableInt
     */
    public function getDuplicateDelay()
    {
        return $this->m_nDuplicateDelay;
    }

    /**
     * @return string
     */
    public function getAVSOverridePolicy()
    {
        return $this->m_szAVSOverridePolicy;
    }

    /**
     * @param string $AVSOverridePolicy
     */
    public function setAVSOverridePolicy($AVSOverridePolicy)
    {
        $this->m_szAVSOverridePolicy = $AVSOverridePolicy;
    }

    /**
     * @return string
     */
    public function getCV2OverridePolicy()
    {
        return $this->m_szCV2OverridePolicy;
    }

    /**
     * @param string $CV2OverridePolicy
     */
    public function setCV2OverridePolicy($CV2OverridePolicy)
    {
        $this->m_szCV2OverridePolicy = $CV2OverridePolicy;
    }

    /**
     * @return NullableBool
     */
    public function getThreeDSecureOverridePolicy()
    {
        return $this->m_boThreeDSecureOverridePolicy;
    }

    /**
     * @return string
     */
    public function getAuthCode()
    {
        return $this->m_szAuthCode;
    }

    /**
     * @param string $authCode
     */
    public function setAuthCode($authCode)
    {
        $this->m_szAuthCode = $authCode;
    }

    /**
     * @return ThreeDSecurePassthroughData
     */
    public function getThreeDSecurePassthroughData()
    {
        return $this->m_tdsptThreeDSecurePassthroughData;
    }

    /**
     * @return GenericVariableList
     */
    public function getCustomVariables()
    {
        return $this->m_lgvCustomVariables;
    }

    //constructor
    public function __construct()
    {
        $this->m_boEchoCardType = new NullableBool();
        $this->m_boEchoAVSCheckResult = new NullableBool();
        $this->m_boEchoCV2CheckResult = new NullableBool();
        $this->m_boEchoAmountReceived = new NullableBool();
        $this->m_nDuplicateDelay = new NullableInt();
        $this->m_szAVSOverridePolicy = '';
        $this->m_szCV2OverridePolicy = '';
        $this->m_boThreeDSecureOverridePolicy = new NullableBool();
        $this->m_szAuthCode = '';
        $this->m_tdsptThreeDSecurePassthroughData = new ThreeDSecurePassthroughData();
        $this->m_lgvCustomVariables = new GenericVariableList();
    }
}
