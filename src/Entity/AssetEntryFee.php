<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetEntryFeeRepository")
 */
class AssetEntryFee
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $min;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $max;

    /**
     * @ORM\ManyToOne(targetEntity=ApplicationSettings::class, inversedBy="assetEntryFees")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $applicationSettings;

    /**
     * @ORM\Column(type="float")
     */
    private $fee;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(?float $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getApplicationSettings(): ?ApplicationSettings
    {
        return $this->applicationSettings;
    }

    public function setApplicationSettings(?ApplicationSettings $applicationSettings): self
    {
        $this->applicationSettings = $applicationSettings;

        return $this;
    }

    public function getFee(): ?float
    {
        return $this->fee;
    }

    public function setFee(float $fee): self
    {
        $this->fee = $fee;

        return $this;
    }
}
