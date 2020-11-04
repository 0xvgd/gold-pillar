<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages;

use App\Utils\TakePayments\Gateway\Common\NullableBool;
use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\Common\SharedFunctions;
use App\Utils\TakePayments\Gateway\Common\XmlTag;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\GenericVariableList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\CardTypeData;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GatewayOutput;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\Issuer;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\PaymentMessageGatewayOutput;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\PreviousTransactionResult;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\ThreeDSecureOutputData;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\TransactionOutputData;

class SharedFunctionsPaymentSystemShared
{
    /**
     * @param GatewayEntryPointList $lgepGatewayEntryPoints
     *
     * @return TransactionOutputData
     *
     * @throws Exception
     */
    public static function getTransactionOutputData(
        XmlTag $xtTransactionOutputDataXmlTag,
        GatewayEntryPointList $lgepGatewayEntryPoints = null
    ) {
        $szCrossReference = '';
        $szAddressNumericCheckResult = '';
        $szPostCodeCheckResult = '';
        $szThreeDSecureAuthenticationCheckResult = '';
        $szCV2CheckResult = '';
        $nAmountReceived = null;
        $szPaREQ = '';
        $szACSURL = '';
        $ctdCardTypeData = null;
        $tdsodThreeDSecureOutputData = null;
        $lgvCustomVariables = null;
        $nCount = 0;
        $szAuthCode = '';
        $todTransactionOutputData = null;

        if (null == $xtTransactionOutputDataXmlTag) {
            return null;
        }

        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.CrossReference',
            $szCrossReference
        );
        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.AuthCode',
            $szAuthCode
        );
        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.AddressNumericCheckResult',
            $szAddressNumericCheckResult
        );
        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.PostCodeCheckResult',
            $szPostCodeCheckResult
        );
        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.ThreeDSecureAuthenticationCheckResult',
            $szThreeDSecureAuthenticationCheckResult
        );
        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.CV2CheckResult',
            $szCV2CheckResult
        );

        $ctdCardTypeData = SharedFunctionsPaymentSystemShared::getCardTypeData(
            $xtTransactionOutputDataXmlTag,
            'TransactionOutputData.CardTypeData'
        );
        if ($xtTransactionOutputDataXmlTag->getIntegerValue(
            'TransactionOutputData.AmountReceived',
            $nTempValue
        )
        ) {
            $nAmountReceived = new NullableInt($nTempValue);
        }
        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.ThreeDSecureOutputData.PaREQ',
            $szPaREQ
        );
        $xtTransactionOutputDataXmlTag->getStringValue(
            'TransactionOutputData.ThreeDSecureOutputData.ACSURL',
            $szACSURL
        );

        if (!SharedFunctions::isStringNullOrEmpty($szACSURL) &&
            !SharedFunctions::isStringNullOrEmpty($szPaREQ)
        ) {
            $tdsodThreeDSecureOutputData = new ThreeDSecureOutputData(
                $szPaREQ,
                $szACSURL
            );
        }

        $nCount = 0;
        $szXmlFormatString1 =
            'TransactionOutputData.CustomVariables.GenericVariable[';
        $szXmlFormatString2 = ']';

        while ($xtTransactionOutputDataXmlTag->getStringValue(
            $szXmlFormatString1.$nCount.$szXmlFormatString2.'.Name',
            $szName
        )
        ) {
            if (!$xtTransactionOutputDataXmlTag->getStringValue(
                $szXmlFormatString1.
                        $nCount.
                        $szXmlFormatString2.
                        '.Value',
                $szValue
            )
            ) {
                $szValue = '';
            }
            if (null == $lgvCustomVariables) {
                $lgvCustomVariables = new GenericVariableList();
            }
            $lgvCustomVariables->add($szName, $szValue);
            ++$nCount;
        }

        $todTransactionOutputData = new TransactionOutputData(
            $szCrossReference,
            $szAuthCode,
            $szAddressNumericCheckResult,
            $szPostCodeCheckResult,
            $szThreeDSecureAuthenticationCheckResult,
            $szCV2CheckResult,
            $ctdCardTypeData,
            $nAmountReceived,
            $tdsodThreeDSecureOutputData,
            $lgvCustomVariables,
            $lgepGatewayEntryPoints
        );

        return $todTransactionOutputData;
    }

    /**
     * @param string $szCardTypeDataXMLPath
     *
     * @return CardTypeData
     *
     * @throws Exception
     */
    public static function getCardTypeData(
        XmlTag $xtGetCardTypeXmlTag,
        $szCardTypeDataXMLPath
    ) {
        $ctdCardTypeData = null;
        $boTempValue = null;
        $nISOCode = null;
        $boLuhnCheckRequired = null;
        $szStartDateStatus = null;
        $szIssueNumberStatus = null;
        $szCardType = null;
        $szIssuer = null;
        $nTemp = null;
        $iIssuer = null;
        $szCardClass = null;

        if (null == $xtGetCardTypeXmlTag) {
            return null;
        }

        if ($xtGetCardTypeXmlTag->getStringValue(
            $szCardTypeDataXMLPath.'.CardType',
            $szCardType
        )
        ) {
            $xtGetCardTypeXmlTag->getStringValue(
                $szCardTypeDataXMLPath.'.CardClass',
                $szCardClass
            );
            $xtGetCardTypeXmlTag->getStringValue(
                $szCardTypeDataXMLPath.'.Issuer',
                $szIssuer
            );
            if ($xtGetCardTypeXmlTag->getIntegerValue(
                $szCardTypeDataXMLPath.'.Issuer.ISOCode',
                $nTemp
            )
            ) {
                $nISOCode = new NullableInt($nTemp);
            }
            $iIssuer = new Issuer($szIssuer, $nISOCode);
            if ($xtGetCardTypeXmlTag->getBooleanValue(
                $szCardTypeDataXMLPath.'.LuhnCheckRequired',
                $boTempValue
            )
            ) {
                $boLuhnCheckRequired = new NullableBool($boTempValue);
            }
            $xtGetCardTypeXmlTag->getStringValue(
                $szCardTypeDataXMLPath.'.IssueNumberStatus',
                $szIssueNumberStatus
            );
            $xtGetCardTypeXmlTag->getStringValue(
                $szCardTypeDataXMLPath.'.StartDateStatus',
                $szStartDateStatus
            );
            $ctdCardTypeData = new CardTypeData(
                $szCardType,
                $szCardClass,
                $iIssuer,
                $boLuhnCheckRequired,
                $szIssueNumberStatus,
                $szStartDateStatus
            );
        }

        return $ctdCardTypeData;
    }

    /**
     * @param string        $szGatewayOutputXMLPath
     * @param GatewayOutput $goGatewayOutput
     *
     * @return PaymentMessageGatewayOutput
     *
     * @throws Exception
     */
    public static function getPaymentMessageGatewayOutput(
        XmlTag $xtMessageResultXmlTag,
        $szGatewayOutputXMLPath,
        GatewayOutput $goGatewayOutput = null
    ) {
        $nTempValue = 0;
        $boAuthorisationAttempted = null;
        $boTempValue = null;
        $nPreviousStatusCode = null;
        $szPreviousMessage = null;
        $ptrPreviousTransactionResult = null;
        $pmgoPaymentMessageGatewayOutput = null;

        if (null == $xtMessageResultXmlTag) {
            return null;
        }

        if ($xtMessageResultXmlTag->getBooleanValue(
            $szGatewayOutputXMLPath.'.AuthorisationAttempted',
            $boTempValue
        )
        ) {
            $boAuthorisationAttempted = new NullableBool($boTempValue);
        }

        // check to see if there is any previous transaction data
        if ($xtMessageResultXmlTag->getIntegerValue(
            $szGatewayOutputXMLPath.
                    '.PreviousTransactionResult.StatusCode',
            $nTempValue
        )
        ) {
            $nPreviousStatusCode = new NullableInt($nTempValue);
        }
        $xtMessageResultXmlTag->getStringValue(
            $szGatewayOutputXMLPath.'.PreviousTransactionResult.Message',
            $szPreviousMessage
        );
        if (null != $nPreviousStatusCode &&
            !SharedFunctions::isStringNullOrEmpty($szPreviousMessage)
        ) {
            $ptrPreviousTransactionResult = new PreviousTransactionResult(
                $nPreviousStatusCode,
                $szPreviousMessage
            );
        }

        $pmgoPaymentMessageGatewayOutput = new PaymentMessageGatewayOutput(
            $goGatewayOutput->getStatusCode(),
            $goGatewayOutput->getMessage(),
            $boAuthorisationAttempted,
            $ptrPreviousTransactionResult,
            $goGatewayOutput->getErrorMessages()
        );

        return $pmgoPaymentMessageGatewayOutput;
    }
}
