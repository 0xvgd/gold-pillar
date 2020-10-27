<?php

namespace App\Enum;

class PostStatus extends LabelledEnum
{
    private const ON_APPROVAL = 'on_approval';
    private const APPROVED = 'approved';
    private const DENIED = 'denied';

    public function getLabel(): string
    {
        switch ($this->getValue()) {
            case self::ON_APPROVAL:
                return 'On Approval';
            case self::APPROVED:
                return 'Approved';
            case self::DENIED:
                return 'Denied';
        }

        return '';
    }

    public static function ON_APPROVAL(): self
    {
        return new PostStatus(self::ON_APPROVAL);
    }

    public static function APPROVED(): self
    {
        return new PostStatus(self::APPROVED);
    }

    public static function DENIED(): self
    {
        return new PostStatus(self::DENIED);
    }
}
