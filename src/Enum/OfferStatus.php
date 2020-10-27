<?php

namespace App\Enum;

class OfferStatus extends LabelledEnum
{
    private const STARTED = 'started';
    private const OFFER_ACCEPTED = 'offer_accepted';
    private const OFFER_DECLINED = 'offer_declined';
    private const COUNTER_OFFER_ACCEPTED = 'co_accepted';
    private const COUNTER_OFFER_DECLINED = 'co_declined';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::STARTED:
                return 'Started';
            case self::OFFER_ACCEPTED:
                return 'Offer accepted';
            case self::OFFER_DECLINED:
                return 'Offer declined';
            case self::COUNTER_OFFER_ACCEPTED:
                return 'Counter offer accepted';
            case self::COUNTER_OFFER_DECLINED:
                return 'Counter offer declined';
        }

        return '';
    }

    public static function STARTED(): self
    {
        return new OfferStatus(self::STARTED);
    }

    public static function OFFER_ACCEPTED(): self
    {
        return new OfferStatus(self::OFFER_ACCEPTED);
    }

    public static function OFFER_DECLINED(): self
    {
        return new OfferStatus(self::OFFER_DECLINED);
    }

    public static function COUNTER_OFFER_ACCEPTED(): self
    {
        return new OfferStatus(self::COUNTER_OFFER_ACCEPTED);
    }

    public static function COUNTER_OFFER_DECLINED(): self
    {
        return new OfferStatus(self::COUNTER_OFFER_DECLINED);
    }
}
