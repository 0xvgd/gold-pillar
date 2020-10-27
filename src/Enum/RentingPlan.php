<?php

namespace App\Enum;

class RentingPlan extends LabelledEnum
{
    private const FIND_ONLY = 'find_only';
    private const RENT_COLLECT = 'rent_collect';
    private const FULLY_MANAGED = 'fully_managed';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::FIND_ONLY:
                return 'Find tenant only';
            case self::RENT_COLLECT:
                return 'Let & Rent collect';
            case self::FULLY_MANAGED:
                return 'Fully managed';
        }

        return '';
    }

    public static function FIND_ONLY(): self
    {
        return new self(self::FIND_ONLY);
    }

    public static function RENT_COLLECT(): self
    {
        return new self(self::RENT_COLLECT);
    }

    public static function FULLY_MANAGED(): self
    {
        return new self(self::FULLY_MANAGED);
    }
}
