<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages;

use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\Common\SharedFunctions;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\CardDetails;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\CustomerDetails;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\TransactionDetails;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\CardDetailsTransactionResult;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\TransactionOutputData;
use App\Utils\TakePayments\Gateway\SOAP\SOAP;

class CardDetailsTransaction extends GatewayTransaction
{
    /**
     * @var TransactionDetails
     */
    private $m_tdTransactionDetails;
    /**
     * @var CardDetails
     */
    private $m_cdCardDetails;
    /**
     * @var CustomerDetails
     */
    private $m_cdCustomerDetails;

    /**
     * @return TransactionDetails
     */
    public function getTransactionDetails()
    {
        return $this->m_tdTransactionDetails;
    }

    /**
     * @return CardDetails
     */
    public function getCardDetails()
    {
        return $this->m_cdCardDetails;
    }

    /**
     * @return CustomerDetails
     */
    public function getCustomerDetails()
    {
        return $this->m_cdCustomerDetails;
    }

    /**
     * @param CardDetailsTransactionResult $cdtrCardDetailsTransactionResult Passed by reference
     * @param TransactionOutputData        $todTransactionOutputData         Passed by reference
     *
     * @return bool
     *
     * @throws Exception
     */
    public function processTransaction(
        CardDetailsTransactionResult &$cdtrCardDetailsTransactionResult = null,
        TransactionOutputData &$todTransactionOutputData = null
    ) {
        $boTransactionSubmitted = false;
        $sSOAPClient;
        $lgepGatewayEntryPoints = null;
        $goGatewayOutput = null;

        $todTransactionOutputData = null;
        $cdtrCardDetailsTransactionResult = null;

        $sSOAPClient = new SOAP(
            'CardDetailsTransaction',
            parent::getSOAPNamespace(),
            $this->getCertDir()
        );

        // transaction details
        if (null != $this->m_tdTransactionDetails) {
            if (null != $this->m_tdTransactionDetails->getAmount()) {
                if ($this->m_tdTransactionDetails->getAmount()->getHasValue()) {
                    $sSOAPClient->addParamAttribute(
                        'PaymentMessage.TransactionDetails',
                        'Amount',
                        (string) $this->m_tdTransactionDetails
                            ->getAmount()
                            ->getValue()
                    );
                }
            }
            if (null != $this->m_tdTransactionDetails->getCurrencyCode()) {
                if ($this->m_tdTransactionDetails
                        ->getCurrencyCode()
                        ->getHasValue()
                ) {
                    $sSOAPClient->addParamAttribute(
                        'PaymentMessage.TransactionDetails',
                        'CurrencyCode',
                        (string) $this->m_tdTransactionDetails
                            ->getCurrencyCode()
                            ->getValue()
                    );
                }
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_tdTransactionDetails->getCaptureEnvironment()
            )
            ) {
                $sSOAPClient->addParamAttribute(
                    'PaymentMessage.TransactionDetails',
                    'CaptureEnvironment',
                    $this->m_tdTransactionDetails->getCaptureEnvironment()
                );
            }
            if (null != $this->m_tdTransactionDetails->getContinuousAuthority()
            ) {
                if ($this->m_tdTransactionDetails
                        ->getContinuousAuthority()
                        ->getHasValue()
                ) {
                    $sSOAPClient->addParamAttribute(
                        'PaymentMessage.TransactionDetails',
                        'ContinuousAuthority',
                        (string) $this->m_tdTransactionDetails
                            ->getContinuousAuthority()
                            ->getValue()
                    );
                }
            }
            if (null != $this->m_tdTransactionDetails->getMessageDetails()) {
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getClientReference()
                )
                ) {
                    $sSOAPClient->addParamAttribute(
                        'PaymentMessage.TransactionDetails.MessageDetails',
                        'ClientReference',
                        $this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getClientReference()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getTransactionType()
                )
                ) {
                    $sSOAPClient->addParamAttribute(
                        'PaymentMessage.TransactionDetails.MessageDetails',
                        'TransactionType',
                        $this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getTransactionType()
                    );
                }
            }
            if (null != $this->m_tdTransactionDetails->getTransactionControl()
            ) {
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getAuthCode()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.TransactionDetails.TransactionControl.AuthCode',
                        $this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getAuthCode()
                    );
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getThreeDSecureOverridePolicy()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getThreeDSecureOverridePolicy()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.ThreeDSecureOverridePolicy',
                            SharedFunctions::boolToString(
                                $this->m_tdTransactionDetails
                                    ->getTransactionControl()
                                    ->getThreeDSecureOverridePolicy()
                                    ->getValue()
                            )
                        );
                    }
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getAVSOverridePolicy()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.TransactionDetails.TransactionControl.AVSOverridePolicy',
                        $this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getAVSOverridePolicy()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getCV2OverridePolicy()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.TransactionDetails.TransactionControl.CV2OverridePolicy',
                        $this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getCV2OverridePolicy()
                    );
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getDuplicateDelay()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getDuplicateDelay()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.DuplicateDelay',
                            (string) $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getDuplicateDelay()
                                ->getValue()
                        );
                    }
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getEchoCardType()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getEchoCardType()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.EchoCardType',
                            SharedFunctions::boolToString(
                                $this->m_tdTransactionDetails
                                    ->getTransactionControl()
                                    ->getEchoCardType()
                                    ->getValue()
                            )
                        );
                    }
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getEchoAVSCheckResult()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getEchoAVSCheckResult()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.EchoAVSCheckResult',
                            SharedFunctions::boolToString(
                                $this->m_tdTransactionDetails
                                    ->getTransactionControl()
                                    ->getEchoAVSCheckResult()
                                    ->getValue()
                            )
                        );
                    }
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getEchoAVSCheckResult()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getEchoAVSCheckResult()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.EchoAVSCheckResult',
                            SharedFunctions::boolToString(
                                $this->m_tdTransactionDetails
                                    ->getTransactionControl()
                                    ->getEchoAVSCheckResult()
                                    ->getValue()
                            )
                        );
                    }
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getEchoCV2CheckResult()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getEchoCV2CheckResult()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.EchoCV2CheckResult',
                            SharedFunctions::boolToString(
                                $this->m_tdTransactionDetails
                                    ->getTransactionControl()
                                    ->getEchoCV2CheckResult()
                                    ->getValue()
                            )
                        );
                    }
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getEchoAmountReceived()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getEchoAmountReceived()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.EchoAmountReceived',
                            SharedFunctions::boolToString(
                                $this->m_tdTransactionDetails
                                    ->getTransactionControl()
                                    ->getEchoAmountReceived()
                                    ->getValue()
                            )
                        );
                    }
                }
                if (null != $this->m_tdTransactionDetails
                        ->getTransactionControl()
                        ->getThreeDSecurePassthroughData()
                ) {
                    if (!SharedFunctions::isStringNullOrEmpty(
                        $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getEnrolmentStatus()
                    )
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.TransactionDetails.TransactionControl.ThreeDSecurePassthroughData',
                            'EnrolmentStatus',
                            $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getEnrolmentStatus()
                        );
                    }
                    if (!SharedFunctions::isStringNullOrEmpty(
                        $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getAuthenticationStatus()
                    )
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.TransactionDetails.TransactionControl.ThreeDSecurePassthroughData',
                            'AuthenticationStatus',
                            $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getAuthenticationStatus()
                        );
                    }
                    if (!SharedFunctions::isStringNullOrEmpty(
                        $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getElectronicCommerceIndicator()
                    )
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.ThreeDSecurePassthroughData.ElectronicCommerceIndicator',
                            $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getElectronicCommerceIndicator()
                        );
                    }
                    if (!SharedFunctions::isStringNullOrEmpty(
                        $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getAuthenticationValue()
                    )
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.ThreeDSecurePassthroughData.AuthenticationValue',
                            $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getAuthenticationValue()
                        );
                    }
                    if (!SharedFunctions::isStringNullOrEmpty(
                        $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getTransactionIdentifier()
                    )
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.TransactionDetails.TransactionControl.ThreeDSecurePassthroughData.TransactionIdentifier',
                            $this->m_tdTransactionDetails
                                ->getTransactionControl()
                                ->getThreeDSecurePassthroughData()
                                ->getTransactionIdentifier()
                        );
                    }
                }
            }
            if (null !=
                $this->m_tdTransactionDetails->getThreeDSecureBrowserDetails()
            ) {
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getThreeDSecureBrowserDetails()
                            ->getAcceptHeaders()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.TransactionDetails.ThreeDSecureBrowserDetails.AcceptHeaders',
                        $this->m_tdTransactionDetails
                            ->getThreeDSecureBrowserDetails()
                            ->getAcceptHeaders()
                    );
                }
                if (null != $this->m_tdTransactionDetails
                        ->getThreeDSecureBrowserDetails()
                        ->getDeviceCategory()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getThreeDSecureBrowserDetails()
                            ->getDeviceCategory()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.TransactionDetails.ThreeDSecureBrowserDetails',
                            'DeviceCategory',
                            (string) $this->m_tdTransactionDetails
                                ->getThreeDSecureBrowserDetails()
                                ->getDeviceCategory()
                                ->getValue()
                        );
                    }
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getThreeDSecureBrowserDetails()
                            ->getUserAgent()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.TransactionDetails.ThreeDSecureBrowserDetails.UserAgent',
                        $this->m_tdTransactionDetails
                            ->getThreeDSecureBrowserDetails()
                            ->getUserAgent()
                    );
                }
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_tdTransactionDetails->getOrderID()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.TransactionDetails.OrderID',
                    $this->m_tdTransactionDetails->getOrderID()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_tdTransactionDetails->getOrderDescription()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.TransactionDetails.OrderDescription',
                    $this->m_tdTransactionDetails->getOrderDescription()
                );
            }
        }
        // card details
        if (null != $this->m_cdCardDetails) {
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_cdCardDetails->getCardName()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CardDetails.CardName',
                    $this->m_cdCardDetails->getCardName()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_cdCardDetails->getCV2()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CardDetails.CV2',
                    $this->m_cdCardDetails->getCV2()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_cdCardDetails->getCardNumber()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CardDetails.CardNumber',
                    $this->m_cdCardDetails->getCardNumber()
                );
            }
            if (null != $this->m_cdCardDetails->getExpiryDate()) {
                if (null != $this->m_cdCardDetails->getExpiryDate()->getMonth()
                ) {
                    if ($this->m_cdCardDetails
                            ->getExpiryDate()
                            ->getMonth()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.CardDetails.ExpiryDate',
                            'Month',
                            (string) $this->m_cdCardDetails
                                ->getExpiryDate()
                                ->getMonth()
                                ->getValue()
                        );
                    }
                }
                if (null != $this->m_cdCardDetails->getExpiryDate()->getYear()
                ) {
                    if ($this->m_cdCardDetails
                            ->getExpiryDate()
                            ->getYear()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.CardDetails.ExpiryDate',
                            'Year',
                            (string) $this->m_cdCardDetails
                                ->getExpiryDate()
                                ->getYear()
                                ->getValue()
                        );
                    }
                }
            }
            if (null != $this->m_cdCardDetails->getStartDate()) {
                if (null != $this->m_cdCardDetails->getStartDate()->getMonth()
                ) {
                    if ($this->m_cdCardDetails
                            ->getStartDate()
                            ->getMonth()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.CardDetails.StartDate',
                            'Month',
                            (string) $this->m_cdCardDetails
                                ->getStartDate()
                                ->getMonth()
                                ->getValue()
                        );
                    }
                }
                if (null != $this->m_cdCardDetails->getStartDate()->getYear()) {
                    if ($this->m_cdCardDetails
                            ->getStartDate()
                            ->getYear()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.CardDetails.StartDate',
                            'Year',
                            (string) $this->m_cdCardDetails
                                ->getStartDate()
                                ->getYear()
                                ->getValue()
                        );
                    }
                }
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_cdCardDetails->getIssueNumber()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CardDetails.IssueNumber',
                    $this->m_cdCardDetails->getIssueNumber()
                );
            }
        }
        // customer details
        if (null != $this->m_cdCustomerDetails) {
            if (null != $this->m_cdCustomerDetails->getBillingAddress()) {
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress1()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.BillingAddress.Address1',
                        $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress1()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress2()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.BillingAddress.Address2',
                        $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress2()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress3()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.BillingAddress.Address3',
                        $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress3()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress4()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.BillingAddress.Address4',
                        $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getAddress4()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getCity()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.BillingAddress.City',
                        $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getCity()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getState()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.BillingAddress.State',
                        $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getState()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getPostCode()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.BillingAddress.PostCode',
                        $this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getPostCode()
                    );
                }
                if (null != $this->m_cdCustomerDetails
                        ->getBillingAddress()
                        ->getCountryCode()
                ) {
                    if ($this->m_cdCustomerDetails
                            ->getBillingAddress()
                            ->getCountryCode()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.CustomerDetails.BillingAddress.CountryCode',
                            (string) $this->m_cdCustomerDetails
                                ->getBillingAddress()
                                ->getCountryCode()
                                ->getValue()
                        );
                    }
                }
            }
            if (null != $this->m_cdCustomerDetails->getPrimaryAccountDetails()
            ) {
                if (null != $this->m_cdCustomerDetails
                        ->getPrimaryAccountDetails()
                        ->getAddressDetails()
                ) {
                    if (!SharedFunctions::isStringNullOrEmpty(
                        $this->m_cdCustomerDetails
                                ->getPrimaryAccountDetails()
                                ->getAddressDetails()
                                ->getPostCode()
                    )
                    ) {
                        $sSOAPClient->addParam(
                            'PaymentMessage.CustomerDetails.PrimaryAccountDetails.AddressDetails.PostCode',
                            (string) $this->m_cdCustomerDetails
                                ->getPrimaryAccountDetails()
                                ->getAddressDetails()
                                ->getPostCode()
                        );
                    }
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getPrimaryAccountDetails()
                            ->getName()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.PrimaryAccountDetails.Name',
                        $this->m_cdCustomerDetails
                            ->getPrimaryAccountDetails()
                            ->getName()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getPrimaryAccountDetails()
                            ->getAccountNumber()
                )
                ) {
                    $sSOAPClient->addParam(
                        'PaymentMessage.CustomerDetails.PrimaryAccountDetails.AccountNumber',
                        $this->m_cdCustomerDetails
                            ->getPrimaryAccountDetails()
                            ->getAccountNumber()
                    );
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_cdCustomerDetails
                            ->getPrimaryAccountDetails()
                            ->getDateOfBirth()
                )
                ) {
                    $sSOAPClient.
                        AddParam(
                            'PaymentMessage.CustomerDetails.PrimaryAccountDetails.DateOfBirth',
                            $this->m_cdCustomerDetails
                                ->getPrimaryAccountDetails()
                                ->getDateOfBirth()
                        );
                }
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_cdCustomerDetails->getEmailAddress()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CustomerDetails.EmailAddress',
                    $this->m_cdCustomerDetails->getEmailAddress()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_cdCustomerDetails->getPhoneNumber()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CustomerDetails.PhoneNumber',
                    $this->m_cdCustomerDetails->getPhoneNumber()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_cdCustomerDetails->getCustomerIPAddress()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CustomerDetails.CustomerIPAddress',
                    $this->m_cdCustomerDetails->getCustomerIPAddress()
                );
            }
        }

        $boTransactionSubmitted = GatewayTransaction::processTransactionBase(
            $sSOAPClient,
            'PaymentMessage',
            'CardDetailsTransactionResult',
            'TransactionOutputData',
            $goGatewayOutput,
            $lgepGatewayEntryPoints
        );

        if ($boTransactionSubmitted) {
            $cdtrCardDetailsTransactionResult = SharedFunctionsPaymentSystemShared::getPaymentMessageGatewayOutput(
                $sSOAPClient->getXmlTag(),
                'CardDetailsTransactionResult',
                $goGatewayOutput
            );

            $todTransactionOutputData = SharedFunctionsPaymentSystemShared::getTransactionOutputData(
                $sSOAPClient->getXmlTag(),
                $lgepGatewayEntryPoints
            );
        }

        return $boTransactionSubmitted;
    }

    /**
     * @param RequestGatewayEntryPointList $lrgepRequestGatewayEntryPoints
     * @param int                          $nRetryAttempts
     * @param NullableInt                  $nTimeout
     */
    public function __construct(
        RequestGatewayEntryPointList $lrgepRequestGatewayEntryPoints = null,
        $nRetryAttempts = 1,
        NullableInt $nTimeout = null
    ) {
        parent::__construct(
            $lrgepRequestGatewayEntryPoints,
            $nRetryAttempts,
            $nTimeout
        );

        $this->m_tdTransactionDetails = new TransactionDetails();
        $this->m_cdCardDetails = new CardDetails();
        $this->m_cdCustomerDetails = new CustomerDetails();
    }
}
