<?php

namespace App\Enum;

class PropertyStatus extends LabelledEnum
{
    private const SOLD = 'sold';
    private const FOR_SALE = 'for_sale';
    private const REMOVED = 'removed';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::SOLD:
                return 'Sold';
            case self::FOR_SALE:
                return 'For Sale';
            case self::REMOVED:
                return 'Removed';
        }

        return '';
    }

    public static function SOLD(): self
    {
        return new PropertyStatus(self::SOLD);
    }

    public static function FOR_SALE(): self
    {
        return new PropertyStatus(self::FOR_SALE);
    }

    public static function REMOVED(): self
    {
        return new PropertyStatus(self::REMOVED);
    }
}
