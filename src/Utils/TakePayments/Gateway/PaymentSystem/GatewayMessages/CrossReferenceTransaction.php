<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages;

use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\CustomerDetails;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\OverrideCardDetails;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\TransactionDetails;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\CrossReferenceTransactionResult;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\TransactionOutputData;
use App\Utils\TakePayments\Gateway\SOAP\SOAP;

class CrossReferenceTransaction extends GatewayTransaction
{
    /**
     * @var TransactionDetails
     */
    private $m_tdTransactionDetails;
    /**
     * @var OverrideCardDetails
     */
    private $m_ocdOverrideCardDetails;
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
     * @return OverrideCardDetails
     */
    public function getOverrideCardDetails()
    {
        return $this->m_ocdOverrideCardDetails;
    }

    /**
     * @return CustomerDetails
     */
    public function getCustomerDetails()
    {
        return $this->m_cdCustomerDetails;
    }

    /**
     * @param CrossReferenceTransactionResult $crtrCrossReferenceTransactionResult Passed by reference
     * @param TransactionOutputData           $todTransactionOutputData            Passed by reference
     *
     * @return bool
     *
     * @throws Exception
     */
    public function processTransaction(
        CrossReferenceTransactionResult &$crtrCrossReferenceTransactionResult = null,
        TransactionOutputData &$todTransactionOutputData = null
    ) {
        $boTransactionSubmitted = false;
        $sSOAPClient;
        $lgepGatewayEntryPoints = null;

        $todTransactionOutputData = null;
        $goGatewayOutput = null;

        $sSOAPClient = new SOAP(
            'CrossReferenceTransaction',
            GatewayTransaction::getSOAPNamespace(),
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
                            ->getPreviousClientReference()
                )
                ) {
                    $sSOAPClient->addParamAttribute(
                        'PaymentMessage.TransactionDetails.MessageDetails',
                        'PreviousClientReference',
                        $this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getPreviousClientReference()
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
                if (null != $this->m_tdTransactionDetails
                        ->getMessageDetails()
                        ->getNewTransaction()
                ) {
                    if ($this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getNewTransaction()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.TransactionDetails.MessageDetails',
                            'NewTransaction',
                            !SharedFunctions::boolToString(
                                $this->m_tdTransactionDetails
                                    ->getMessageDetails()
                                    ->getNewTransaction()
                                    ->getValue()
                            )
                        );
                    }
                }
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getCrossReference()
                )
                ) {
                    $sSOAPClient->addParamAttribute(
                        'PaymentMessage.TransactionDetails.MessageDetails',
                        'CrossReference',
                        $this->m_tdTransactionDetails
                            ->getMessageDetails()
                            ->getCrossReference()
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
                if (!SharedFunctions::isStringNullOrEmpty(
                    $this->m_tdTransactionDetails
                            ->getTransactionControl()
                            ->getThreeDSecureOverridePolicy()
                )
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
        if (null != $this->m_ocdOverrideCardDetails) {
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_ocdOverrideCardDetails->getCardName()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.OverrideCardDetails.CardName',
                    $this->m_ocdOverrideCardDetails->getCardName()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_ocdOverrideCardDetails->getCV2()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.OverrideCardDetails.CV2',
                    $this->m_ocdOverrideCardDetails->getCV2()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_ocdOverrideCardDetails->getCardNumber()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.OverrideCardDetails.CardNumber',
                    $this->m_ocdOverrideCardDetails->getCardNumber()
                );
            }
            if (null != $this->m_ocdOverrideCardDetails->getExpiryDate()) {
                if (null != $this->m_ocdOverrideCardDetails
                        ->getExpiryDate()
                        ->getMonth()
                ) {
                    if ($this->m_ocdOverrideCardDetails
                            ->getExpiryDate()
                            ->getMonth()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.OverrideCardDetails.ExpiryDate',
                            'Month',
                            (string) $this->m_ocdOverrideCardDetails
                                ->getExpiryDate()
                                ->getMonth()
                                ->getValue()
                        );
                    }
                }
                if (null != $this->m_ocdOverrideCardDetails
                        ->getExpiryDate()
                        ->getYear()
                ) {
                    if ($this->m_ocdOverrideCardDetails
                            ->getExpiryDate()
                            ->getYear()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.OverrideCardDetails.ExpiryDate',
                            'Year',
                            (string) $this->m_ocdOverrideCardDetails
                                ->getExpiryDate()
                                ->getYear()
                                ->getValue()
                        );
                    }
                }
            }
            if (null != $this->m_ocdOverrideCardDetails->getStartDate()) {
                if (null != $this->m_ocdOverrideCardDetails
                        ->getStartDate()
                        ->getMonth()
                ) {
                    if ($this->m_ocdOverrideCardDetails
                            ->getStartDate()
                            ->getMonth()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.OverrideCardDetails.StartDate',
                            'Month',
                            (string) $this->m_ocdOverrideCardDetails
                                ->getStartDate()
                                ->getMonth()
                                ->getValue()
                        );
                    }
                }
                if (null != $this->m_ocdOverrideCardDetails
                        ->getStartDate()
                        ->getYear()
                ) {
                    if ($this->m_ocdOverrideCardDetails
                            ->getStartDate()
                            ->getYear()
                            ->getHasValue()
                    ) {
                        $sSOAPClient->addParamAttribute(
                            'PaymentMessage.OverrideCardDetails.StartDate',
                            'Year',
                            (string) $this->m_ocdOverrideCardDetails
                                ->getStartDate()
                                ->getYear()
                                ->getValue()
                        );
                    }
                }
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_ocdOverrideCardDetails->getIssueNumber()
            )
            ) {
                $sSOAPClient->addParam(
                    'PaymentMessage.CardDetails.IssueNumber',
                    $this->m_ocdOverrideCardDetails->getIssueNumber()
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
                        (string) $this->m_cdCustomerDetails
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
            'CrossReferenceTransactionResult',
            'TransactionOutputData',
            $goGatewayOutput,
            $lgepGatewayEntryPoints
        );

        if ($boTransactionSubmitted) {
            $crtrCrossReferenceTransactionResult = SharedFunctionsPaymentSystemShared::getPaymentMessageGatewayOutput(
                $sSOAPClient->getXmlTag(),
                'CrossReferenceTransactionResult',
                $goGatewayOutput
            );

            $todTransactionOutputData = SharedFunctionsPaymentSystemShared::getTransactionOutputData(
                $sSOAPClient->getXmlTag(),
                $lgepGatewayEntryPoints
            );
        }

        return $boTransactionSubmitted;
    }

    //constructor

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
        GatewayTransaction::__construct(
            $lrgepRequestGatewayEntryPoints,
            $nRetryAttempts,
            $nTimeout
        );

        $this->m_tdTransactionDetails = new TransactionDetails();
        $this->m_ocdOverrideCardDetails = new OverrideCardDetails();
        $this->m_cdCustomerDetails = new CustomerDetails();
    }
}
