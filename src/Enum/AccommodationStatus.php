<?php

namespace App\Enum;

class AccommodationStatus extends LabelledEnum
{
    private const TO_RENT = 'to_rent';
    private const RESERVED = 'reserved';
    private const RENTED = 'rented';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::TO_RENT:
                return 'To rent';
            case self::RESERVED:
                return 'Reserved';
            case self::RENTED:
                return 'Rented';
        }

        return '';
    }

    public static function TO_RENT(): self
    {
        return new AccommodationStatus(self::TO_RENT);
    }

    public static function RESERVED(): self
    {
        return new AccommodationStatus(self::RESERVED);
    }

    public static function RENTED(): self
    {
        return new AccommodationStatus(self::RENTED);
    }
}
