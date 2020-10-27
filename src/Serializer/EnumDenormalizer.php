<?php

namespace App\Serializer;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class EnumDenormalizer implements ContextAwareDenormalizerInterface
{
    public function denormalize($enum, string $type, ?string $format = null, array $context = [])
    {
        return new $type($enum);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = [])
    {
        return is_subclass_of($type, Enum::class);
    }
}
