<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages;

use App\Utils\TakePayments\Gateway\Common\NullableInt;
use App\Utils\TakePayments\Gateway\Common\SharedFunctions;
use App\Utils\TakePayments\Gateway\Common\StringList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\MerchantAuthentication;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPoint;
use App\Utils\TakePayments\Gateway\PaymentSystem\Input\RequestGatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GatewayEntryPointList;
use App\Utils\TakePayments\Gateway\PaymentSystem\Output\GatewayOutput;
use App\Utils\TakePayments\Gateway\SOAP\SOAP;

abstract class GatewayTransaction
{
    /**
     * @var MerchantAuthentication
     */
    private $m_maMerchantAuthentication;
    /**
     * @var RequestGatewayEntryPointList
     */
    private $m_lrgepRequestGatewayEntryPoints;
    /**
     * @var int
     */
    private $m_nRetryAttempts;
    /**
     * @var NullableInt
     */
    private $m_nTimeout;
    /**
     * @var string
     */
    private $m_szSOAPNamespace = 'https://www.thepaymentgateway.net/';
    /**
     * @var string
     */
    private $m_szLastRequest;
    /**
     * @var string
     */
    private $m_szLastResponse;
    /**
     * @var Exception
     */
    private $m_eLastException;
    /**
     * @var string
     */
    private $m_szEntryPointUsed;

    private $certDir;

    /**
     * @return MerchantAuthentication
     */
    public function getMerchantAuthentication()
    {
        return $this->m_maMerchantAuthentication;
    }

    /**
     * @return RequestGatewayEntryPointList
     */
    public function getRequestGatewayEntryPoints()
    {
        return $this->m_lrgepRequestGatewayEntryPoints;
    }

    /**
     * @return int
     */
    public function getRetryAttempts()
    {
        return $this->m_nRetryAttempts;
    }

    /**
     * @return NullableInt
     */
    public function getTimeout()
    {
        return $this->m_nTimeout;
    }

    /**
     * @return string
     */
    public function getSOAPNamespace()
    {
        return $this->m_szSOAPNamespace;
    }

    /**
     * @param string $value
     */
    public function setSOAPNamespace($value)
    {
        $this->m_szSOAPNamespace = $value;
    }

    /**
     * @return string
     */
    public function getLastRequest()
    {
        return $this->m_szLastRequest;
    }

    /**
     * @return string
     */
    public function getLastResponse()
    {
        return $this->m_szLastResponse;
    }

    /**
     * @return Exception
     */
    public function getLastException()
    {
        return $this->m_eLastException;
    }

    /**
     * @return string
     */
    public function getEntryPointUsed()
    {
        return $this->m_szEntryPointUsed;
    }

    /**
     * @param RequestGatewayEntryPoint $x
     * @param RequestGatewayEntryPoint $y
     *
     * @return int
     */
    public static function compare($x, $y)
    {
        $rgepFirst = null;
        $rgepSecond = null;

        $rgepFirst = $x;
        $rgepSecond = $y;

        return GatewayTransaction::compareGatewayEntryPoints(
            $rgepFirst,
            $rgepSecond
        );
    }

    /**
     * @return int
     */
    private static function compareGatewayEntryPoints(
        RequestGatewayEntryPoint $rgepFirst,
        RequestGatewayEntryPoint $rgepSecond
    ) {
        $nReturnValue = 0;
        // returns >0 if rgepFirst greater than rgepSecond
        // returns 0 if they are equal
        // returns <0 if rgepFirst less than rgepSecond

        // both null, then they are the same
        if (null == $rgepFirst && null == $rgepSecond) {
            $nReturnValue = 0;
        }
        // just first null? then second is greater
        elseif (null == $rgepFirst && null != $rgepSecond) {
            $nReturnValue = 1;
        }
        // just second null? then first is greater
        elseif (null != $rgepFirst && null == $rgepSecond) {
            $nReturnValue = -1;
        }
        // can now assume that first & second both have a value
        elseif ($rgepFirst->getMetric() == $rgepSecond->getMetric()) {
            $nReturnValue = 0;
        } elseif ($rgepFirst->getMetric() < $rgepSecond->getMetric()) {
            $nReturnValue = -1;
        } elseif ($rgepFirst->getMetric() > $rgepSecond->getMetric()) {
            $nReturnValue = 1;
        }

        return $nReturnValue;
    }

    /**
     * @param string                $szMessageXMLPath
     * @param string                $szGatewayOutputXMLPath
     * @param string                $szTransactionMessageXMLPath
     * @param GatewayOutput         $goGatewayOutput
     * @param GatewayEntryPointList $lgepGatewayEntryPoints
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function processTransactionBase(
        SOAP $sSOAPClient,
        $szMessageXMLPath,
        $szGatewayOutputXMLPath,
        $szTransactionMessageXMLPath,
        GatewayOutput &$goGatewayOutput = null,
        GatewayEntryPointList &$lgepGatewayEntryPoints = null
    ) {
        $boTransactionSubmitted = false;
        $nOverallRetryCount = 0;
        $nOverallGatewayEntryPointCount = 0;
        $nGatewayEntryPointCount = 0;
        $nErrorMessageCount = 0;
        $rgepCurrentGatewayEntryPoint = null;
        $nStatusCode = null;
        $szMessage = null;
        $lszErrorMessages = null;
        $szString = null;
        $sbXMLString = null;
        $szXMLFormatString = null;
        $nCount = 0;
        $szEntryPointURL = null;
        $nMetric = null;
        $gepGatewayEntryPoint = null;

        $lgepGatewayEntryPoints = null;
        $goGatewayOutput = null;

        $this->m_szEntryPointUsed = null;

        if (null == $sSOAPClient) {
            return false;
        }

        // populate the merchant details
        if (null != $this->m_maMerchantAuthentication) {
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_maMerchantAuthentication->getMerchantID()
            )
            ) {
                $sSOAPClient->addParamAttribute(
                    $szMessageXMLPath.'.MerchantAuthentication',
                    'MerchantID',
                    $this->m_maMerchantAuthentication->getMerchantID()
                );
            }
            if (!SharedFunctions::isStringNullOrEmpty(
                $this->m_maMerchantAuthentication->getPassword()
            )
            ) {
                $sSOAPClient->addParamAttribute(
                    $szMessageXMLPath.'.MerchantAuthentication',
                    'Password',
                    $this->m_maMerchantAuthentication->getPassword()
                );
            }
        }

        // first need to sort the gateway entry points into the correct usage order
        $number = $this->m_lrgepRequestGatewayEntryPoints->sort(
            'App\Utils\TakePayments\Gateway\PaymentSystem\GatewayMessages\GatewayTransaction',
            'Compare'
        );

        // loop over the overall number of transaction attempts
        while (!$boTransactionSubmitted &&
            $nOverallRetryCount < $this->m_nRetryAttempts
        ) {
            $nOverallGatewayEntryPointCount = 0;

            // loop over the number of gateway entry points in the list
            while (!$boTransactionSubmitted &&
                $nOverallGatewayEntryPointCount <
                    $this->m_lrgepRequestGatewayEntryPoints->getCount()
            ) {
                $rgepCurrentGatewayEntryPoint = $this->m_lrgepRequestGatewayEntryPoints->getAt(
                    $nOverallGatewayEntryPointCount
                );

                // ignore if the metric is "-1" this indicates that the entry point is offline
                if ($rgepCurrentGatewayEntryPoint->getMetric() >= 0) {
                    $nGatewayEntryPointCount = 0;
                    $sSOAPClient->setURL(
                        $rgepCurrentGatewayEntryPoint->getEntryPointURL()
                    );

                    // loop over the number of times to try this specific entry point
                    while (!$boTransactionSubmitted &&
                        $nGatewayEntryPointCount <
                            $rgepCurrentGatewayEntryPoint->getRetryAttempts()
                    ) {
                        if ($sSOAPClient->sendRequest()) {
                            if ($sSOAPClient
                                    ->getXmlTag()
                                    ->getIntegerValue(
                                        $szGatewayOutputXMLPath.'.StatusCode',
                                        $nStatusCode
                                    )
                            ) {
                                // a status code of 50 means that this entry point is not to be used
                                if (50 != $nStatusCode) {
                                    $this->m_szEntryPointUsed = $rgepCurrentGatewayEntryPoint->getEntryPointURL();

                                    // the transaction was submitted
                                    $boTransactionSubmitted = true;

                                    $sSOAPClient
                                        ->getXmlTag()
                                        ->getStringValue(
                                            $szGatewayOutputXMLPath.
                                                '.Message',
                                            $szMessage
                                        );

                                    $nErrorMessageCount = 0;
                                    $lszErrorMessages = new StringList();
                                    $szXmlFormatString1 =
                                        $szGatewayOutputXMLPath.
                                        '.ErrorMessages.MessageDetail[';
                                    $szXmlFormatString2 = '].Detail';

                                    while ($sSOAPClient
                                            ->getXmlTag()
                                            ->getStringValue(
                                                $szXmlFormatString1.
                                                    $nErrorMessageCount.
                                                    $szXmlFormatString2,
                                                $szString
                                            )
                                    ) {
                                        $lszErrorMessages->add($szString);

                                        ++$nErrorMessageCount;
                                    }

                                    $goGatewayOutput = new GatewayOutput(
                                        $nStatusCode,
                                        $szMessage,
                                        $lszErrorMessages
                                    );

                                    // look to see if there are any gateway entry points
                                    $nCount = 0;
                                    $szXmlFormatString1 =
                                        $szTransactionMessageXMLPath.
                                        '.GatewayEntryPoints.GatewayEntryPoint[';
                                    $szXmlFormatString2 = ']';

                                    while ($sSOAPClient
                                            ->getXmlTag()
                                            ->getStringValue(
                                                $szXmlFormatString1.
                                                    $nCount.
                                                    $szXmlFormatString2.
                                                    '.EntryPointURL',
                                                $szEntryPointURL
                                            )
                                    ) {
                                        if (!$sSOAPClient
                                                ->getXmlTag()
                                                ->getIntegerValue(
                                                    $szXmlFormatString1.
                                                        $nCount.
                                                        $szXmlFormatString2.
                                                        '.Metric',
                                                    $nMetric
                                                )
                                        ) {
                                            $nMetric = -1;
                                        }
                                        if (null == $lgepGatewayEntryPoints) {
                                            $lgepGatewayEntryPoints = new GatewayEntryPointList();
                                        }
                                        $lgepGatewayEntryPoints->add(
                                            $szEntryPointURL,
                                            $nMetric
                                        );
                                        ++$nCount;
                                    }
                                }
                            }
                        }

                        ++$nGatewayEntryPointCount;
                    }
                }
                ++$nOverallGatewayEntryPointCount;
            }
            ++$nOverallRetryCount;
        }
        $this->m_szLastRequest = $sSOAPClient->getSOAPPacket();
        $this->m_szLastResponse = $sSOAPClient->getLastResponse();
        $this->m_eLastException = $sSOAPClient->getLastException();

        return $boTransactionSubmitted;
    }

    public function __construct(
        RequestGatewayEntryPointList $lrgepRequestGatewayEntryPoints = null,
        $nRetryAttempts = 1,
        NullableInt $nTimeout = null,
        string $certDir = null
    ) {
        $this->m_maMerchantAuthentication = new MerchantAuthentication();
        $this->m_lrgepRequestGatewayEntryPoints = $lrgepRequestGatewayEntryPoints;
        $this->m_nRetryAttempts = $nRetryAttempts;
        $this->m_nTimeout = $nTimeout;
        $this->certDir = $certDir;
    }

    /**
     * Get the value of certDir.
     */
    public function getCertDir()
    {
        return $this->certDir;
    }

    /**
     * Set the value of certDir.
     *
     * @return self
     */
    public function setCertDir($certDir)
    {
        $this->certDir = $certDir;

        return $this;
    }
}
