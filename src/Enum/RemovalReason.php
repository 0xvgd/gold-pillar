<?php

namespace App\Enum;

class RemovalReason extends LabelledEnum
{
    private const MADE_THE_SALE = 'made_the_sale';
    private const WONT_SELL_ANYMORE = 'wont_sell_anymore';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::MADE_THE_SALE:
                return 'Made the Sale';
            case self::WONT_SELL_ANYMORE:
                return 'Wont Sell Anymore';
        }

        return '';
    }

    public static function MADE_THE_SALE(): self
    {
        return new RemovalReason(self::MADE_THE_SALE);
    }

    public static function WONT_SELL_ANYMORE(): self
    {
        return new RemovalReason(self::WONT_SELL_ANYMORE);
    }
}
