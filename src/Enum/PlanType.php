<?php

namespace App\Enum;

class PlanType extends LabelledEnum
{
    private const AGENT = 'agent';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::AGENT:
                return 'Agent';
        }

        return '';
    }

    public static function AGENT(): self
    {
        return new PlanType(self::AGENT);
    }
}
