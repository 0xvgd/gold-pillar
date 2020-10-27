<?php

namespace App\Enum;

class AssetType extends LabelledEnum
{
    private const STUDENT = 'student';
    private const COMMERCIAL = 'commercial';
    private const RESIDENTIAL = 'residential';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::STUDENT:
                return 'Student';
            case self::COMMERCIAL:
                return 'Commercial';
            case self::RESIDENTIAL:
                return 'Residential';
        }

        return '';
    }

    public static function STUDENT(): self
    {
        return new AssetType(self::STUDENT);
    }

    public static function COMMERCIAL(): self
    {
        return new AssetType(self::COMMERCIAL);
    }

    public static function RESIDENTIAL(): self
    {
        return new AssetType(self::RESIDENTIAL);
    }
}
