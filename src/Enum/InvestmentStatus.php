<?php

namespace App\Enum;

class InvestmentStatus extends LabelledEnum
{
    private const WAITING = 'waiting';
    private const APPROVED = 'approved';
    private const DENIED = 'denied';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::WAITING:
                return 'Waiting';
            case self::APPROVED:
                return 'Approved';
            case self::DENIED:
                return 'Denied';
        }

        return '';
    }

    public static function WAITING(): self
    {
        return new self(self::WAITING);
    }

    public static function APPROVED(): self
    {
        return new self(self::APPROVED);
    }

    public static function DENIED(): self
    {
        return new self(self::DENIED);
    }
}
