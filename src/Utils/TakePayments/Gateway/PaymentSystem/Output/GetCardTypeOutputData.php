<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Output;

class GetCardTypeOutputData extends BaseOutputData
{
    /**
     * @var CardTypeData
     */
    private $m_ctdCardTypeData;

    /**
     * @return CardTypeData
     */
    public function getCardTypeData()
    {
        return $this->m_ctdCardTypeData;
    }

    //constructor

    /**
     * @param CardTypeData          $ctdCardTypeData
     * @param GatewayEntryPointList $lgepGatewayEntryPoints
     */
    public function __construct(
        CardTypeData $ctdCardTypeData = null,
        GatewayEntryPointList $lgepGatewayEntryPoints = null
    ) {
        parent::__construct($lgepGatewayEntryPoints);

        $this->m_ctdCardTypeData = $ctdCardTypeData;
    }
}
