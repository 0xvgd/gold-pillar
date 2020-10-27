<?php

namespace App\Enum;

class ProjectStatus extends LabelledEnum
{
    private const TO_INVEST = 'to_invest';
    private const FUNDED = 'funded';
    private const PENDING = 'pending';
    private const CANCELED = 'canceled';
    private const DECLINED = 'declined';
    private const SOLD = 'sold';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::TO_INVEST:
                return 'To Invest';
            case self::FUNDED:
                return 'Funded';
            case self::PENDING:
                return 'Pending';
            case self::CANCELED:
                return 'Canceled';
            case self::DECLINED:
                return 'Declined';
            case self::SOLD:
                return 'Sold';
        }

        return '';
    }

    public static function TO_INVEST(): self
    {
        return new ProjectStatus(self::TO_INVEST);
    }

    public static function FUNDED(): self
    {
        return new ProjectStatus(self::FUNDED);
    }

    public static function PENDING(): self
    {
        return new ProjectStatus(self::PENDING);
    }

    public static function CANCELED(): self
    {
        return new ProjectStatus(self::CANCELED);
    }

    public static function DECLINED(): self
    {
        return new ProjectStatus(self::DECLINED);
    }

    public static function SOLD(): self
    {
        return new ProjectStatus(self::SOLD);
    }
}
