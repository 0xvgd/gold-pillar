<?php

namespace App\Entity;

use App\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Table(name="investor_profits")
 * @ORM\Entity(repositoryClass="App\Repository\InvestorProfitRepository")
 */
class InvestorProfit
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
    private $profit;

    /**
     * @ORM\ManyToOne(targetEntity=ApplicationSettings::class, inversedBy="investorProfits")
     */
    private $applicationSettings;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="investorProfits")
     */
    private $company;

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

    public function getProfit(): ?float
    {
        return $this->profit;
    }

    public function setProfit(float $profit): self
    {
        $this->profit = $profit;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
