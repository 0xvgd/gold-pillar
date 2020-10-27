<?php

namespace App\Enum;

class TermType extends LabelledEnum
{
    private const DAILY = 'days';
    private const WEEKLY = 'weeks';
    private const MONTHLY = 'months';
    private const YEARLY = 'years';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::DAILY:
                return 'Days';
            case self::WEEKLY:
                return 'Weeks';
            case self::MONTHLY:
                return 'Months';
            case self::YEARLY:
                return 'Years';
        }

        return '';
    }

    public static function DAILY(): self
    {
        return new TermType(self::DAILY());
    }

    public static function WEEKLY(): self
    {
        return new TermType(self::WEEKLY);
    }

    public static function MONTHLY(): self
    {
        return new TermType(self::MONTHLY);
    }

    public static function YEARLY(): self
    {
        return new TermType(self::YEARLY);
    }
}
