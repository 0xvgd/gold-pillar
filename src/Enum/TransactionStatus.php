<?php

namespace App\Enum;

class TransactionStatus extends LabelledEnum
{
    private const CREATED = 'created';
    private const PROCESSED = 'processed';
    private const CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::CREATED:
                return 'Created';
            case self::PROCESSED:
                return 'Processed';
            case self::CANCELLED:
                return 'Cancelled';
        }

        return '';
    }

    public static function CREATED(): self
    {
        return new self(self::CREATED);
    }

    public static function PROCESSED(): self
    {
        return new self(self::PROCESSED);
    }

    public static function CANCELLED(): self
    {
        return new self(self::CANCELLED);
    }
}
