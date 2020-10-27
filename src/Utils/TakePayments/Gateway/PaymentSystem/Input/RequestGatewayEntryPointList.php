<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use Exception;

class RequestGatewayEntryPointList
{
    /**
     * @var RequestGatewayEntryPoint[]
     */
    private $m_lrgepRequestGatewayEntryPoint;

    /**
     * @param int $nIndex
     *
     * @return RequestGatewayEntryPoint
     *
     * @throws Exception
     */
    public function getAt($nIndex)
    {
        if ($nIndex < 0 ||
            $nIndex >= count($this->m_lrgepRequestGatewayEntryPoint)
        ) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lrgepRequestGatewayEntryPoint[$nIndex];
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lrgepRequestGatewayEntryPoint);
    }

    /**
     * @param string $ComparerClassName
     * @param string $ComparerMethodName
     */
    public function sort($ComparerClassName, $ComparerMethodName)
    {
        usort($this->m_lrgepRequestGatewayEntryPoint, [
            "$ComparerClassName",
            "$ComparerMethodName",
        ]);
    }

    /**
     * @param string $EntryPointURL
     * @param int    $nMetric
     * @param int    $nRetryAttempts
     */
    public function add($EntryPointURL, $nMetric, $nRetryAttempts)
    {
        array_push(
            $this->m_lrgepRequestGatewayEntryPoint,
            new RequestGatewayEntryPoint(
                $EntryPointURL,
                $nMetric,
                $nRetryAttempts
            )
        );
    }

    //constructor
    public function __construct()
    {
        $this->m_lrgepRequestGatewayEntryPoint = [];
    }
}
