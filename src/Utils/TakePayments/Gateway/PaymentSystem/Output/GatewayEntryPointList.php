<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

use App\Utils\TakePayments\Gateway\Common\XmlParser;
use Exception;

class GatewayEntryPointList
{
    /**
     * @var GatewayEntryPoint[]
     */
    private $m_lgepGatewayEntryPoint;

    /**
     * @param int $nIndex
     *
     * @return GatewayEntryPoint
     *
     * @throws Exception
     */
    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_lgepGatewayEntryPoint)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lgepGatewayEntryPoint[$nIndex];
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lgepGatewayEntryPoint);
    }

    /**
     * @param string $GatewayEntrypointURL
     * @param int    $nMetric
     */
    public function add($GatewayEntrypointURL, $nMetric)
    {
        array_push(
            $this->m_lgepGatewayEntryPoint,
            new GatewayEntryPoint($GatewayEntrypointURL, $nMetric)
        );
    }

    /**
     * @param string $szXmlString
     *
     * @return GatewayEntryPointList
     *
     * @throws Exception
     */
    public static function fromXmlString($szXmlString)
    {
        $xpXmlParser = new XmlParser();

        if (!$xpXmlParser->parseBuffer($szXmlString)) {
            throw new Exception('Could not parse response string');
        } else {
            // look to see if there are any gateway entry points
            $nCount = 0;
            $szXmlFormatString1 = 'GatewayEntryPoint[';
            $szXmlFormatString2 = ']';
            $lgepGatewayEntryPoints = null;

            while ($xpXmlParser->getStringValue(
                $szXmlFormatString1.
                        $nCount.
                        $szXmlFormatString2.
                        '.EntryPointURL',
                $szEntryPointURL
            )
            ) {
                if (!$xpXmlParser->getIntegerValue(
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
                $lgepGatewayEntryPoints->add($szEntryPointURL, $nMetric);
                ++$nCount;
            }
        }

        return $lgepGatewayEntryPoints;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function toXmlString()
    {
        $szXmlString = '<GatewayEntryPoints>';

        for ($nCount = 0; $nCount < $this->getCount(); ++$nCount) {
            $szXmlString = $szXmlString.$this->getAt($nCount)->toXmlString();
        }
        $szXmlString = $szXmlString.'</GatewayEntryPoints>';

        return $szXmlString;
    }

    //constructor
    public function __construct()
    {
        $this->m_lgepGatewayEntryPoint = [];
    }
}
