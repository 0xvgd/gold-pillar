<?php

namespace App\Serializer;

use App\Enum\LabelledEnum;
use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class EnumNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @param LabelledEnum $enum
     */
    public function normalize($enum, $format = null, array $context = [])
    {
        return [
            'value' => $enum->getValue(),
            'label' => $enum->getLabel(),
        ];
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Enum;
    }
}
