<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetInvestmentClassRepository")
 */
class AssetInvestmentClass
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
    private $minAmount;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $maxAmount;

    /**
     * @ORM\Column(type="float")
     */
    private $equity;

    /**
     * @ORM\ManyToOne(targetEntity=ApplicationSettings::class, inversedBy="assetInvestmentClasses")
     */
    private $applicationSettings;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $description;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getMinAmount(): ?float
    {
        return $this->minAmount;
    }

    public function setMinAmount(float $minAmount): self
    {
        $this->minAmount = $minAmount;

        return $this;
    }

    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(?float $maxAmount): self
    {
        $this->maxAmount = $maxAmount;

        return $this;
    }

    public function getEquity(): ?float
    {
        return $this->equity;
    }

    public function setEquity(float $equity): self
    {
        $this->equity = $equity;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
