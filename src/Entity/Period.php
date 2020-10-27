<?php

namespace App\Entity;

use App\Enum\PeriodUnit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Period
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $value;

    /**
     * @var PeriodUnit
     *
     * @ORM\Column(type=PeriodUnit::class, length=20, nullable=true)
     */
    private $unit;

    public function getUnit(): ?PeriodUnit
    {
        return $this->unit;
    }

    public function setUnit(?PeriodUnit $unit)
    {
        $this->unit = $unit;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
