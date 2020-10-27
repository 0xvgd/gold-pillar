<?php

namespace App\Enum;

class PeriodUnit extends LabelledEnum
{
    private const HOUR = 'hour';
    private const MINUTE = 'minute';
    private const DAY = 'day';
    private const WEEK = 'week';
    private const MONTH = 'month';
    private const YEAR = 'year';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::HOUR:
                return 'Hour';
            case self::MINUTE:
                return 'Minute';
            case self::DAY:
                return 'Day';
            case self::WEEK:
                return 'Week';
            case self::MONTH:
                return 'Month';
            case self::YEAR:
                return 'Year';
        }

        return '';
    }

    public static function HOUR(): self
    {
        return new PeriodUnit(self::HOUR);
    }

    public static function MINUTE(): self
    {
        return new PeriodUnit(self::MINUTE);
    }

    public static function DAY(): self
    {
        return new PeriodUnit(self::DAY);
    }

    public static function WEEK(): self
    {
        return new PeriodUnit(self::WEEK);
    }

    public static function MONTH(): self
    {
        return new PeriodUnit(self::MONTH);
    }

    public static function YEAR(): self
    {
        return new PeriodUnit(self::YEAR);
    }
}
