<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

abstract class LabelledEnum extends Enum implements \JsonSerializable
{
    abstract public function getLabel(): string;

    public function jsonSerialize()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
        ];
    }
}
