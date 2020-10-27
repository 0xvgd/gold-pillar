<?php

namespace App\Enum;

class CalendarEventType extends LabelledEnum
{
    private const BUSINESS = 'business';
    private const PERSONAL = 'personal';
    private const VIEW = 'view';
    private const MEETING = 'meeting';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::BUSINESS:
                return 'Business';
            case self::PERSONAL:
                return 'Personal';
            case self::VIEW:
                return 'View';
            case self::MEETING:
                return 'Meeting';
        }

        return '';
    }

    public static function BUSINESS(): self
    {
        return new self(self::BUSINESS);
    }

    public static function PERSONAL(): self
    {
        return new self(self::PERSONAL);
    }

    public static function VIEW(): self
    {
        return new self(self::VIEW);
    }

    public static function MEETING(): self
    {
        return new self(self::MEETING);
    }
}
