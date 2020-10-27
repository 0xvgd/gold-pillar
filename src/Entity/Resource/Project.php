<?php

namespace App\Entity\Resource;

use App\Entity\Money;
use App\Entity\Person\Broker;
use App\Entity\Person\Contractor;
use App\Entity\Person\Engineer;
use App\Enum\ProjectStatus;
use App\Enum\ProjectType;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="resource_projects")
 * @ORM\EntityListeners({"App\EntityListener\Project\ProjectListener"})
 * @ORM\Entity(repositoryClass="App\Repository\Project\ProjectRepository")
 */
class Project extends Resource implements InvestiblaInterface
{
    /**
     * @var ProjectStatus
     *
     * @ORM\Column(type=ProjectStatus::class, length=20)
     * @Groups({"read", "list"})
     */
    private $projectStatus;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="purchase_price_")
     * @Groups({"read", "list"})
     */
    private $purchasePrice;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="sale_price_projection_")
     * @Groups({"read", "list"})
     */
    private $salePriceProjection;

    /**
     * @var ProjectType
     *
     * @ORM\Column(type=ProjectType::class, length=20)
     * @Groups({"read", "list"})
     */
    private $projectType;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="sale_real_price_")
     * @Groups({"read", "list"})
     */
    private $saleRealPrice;

    /**
     * @var Engineer
     *
     * @ORM\ManyToOne(targetEntity=Engineer::class, inversedBy="projects")
     * @Groups({"read"})
     */
    private $engineer;

    /**
     * @var Broker
     *
     * @ORM\ManyToOne(targetEntity=Broker::class, inversedBy="projects", cascade={"persist"})
     * @Groups({"read"})
     */
    private $broker;

    /**
     * @var Contractor
     * @ORM\ManyToOne(targetEntity=Contractor::class, inversedBy="projects")
     * @Groups({"read"})
     */
    private $contractor;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read"})
     */
    private $paymentsProcessedAt;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="construction_cost_")
     * @Groups({"read", "list"})
     */
    private $constructionCost;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read", "list"})
     */
    private $bedrooms;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read", "list"})
     */
    private $bathrooms;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="total_invested_")
     * @Groups({"read", "list"})
     */
    private $totalInvested;

    public function __construct()
    {
        parent::__construct();
        $this->purchasePrice = new Money();
        $this->salePriceProjection = new Money();
        $this->saleRealPrice = new Money();
        $this->saleRealPrice->setAmount(0);
        $this->constructionCost = new Money();
        $this->totalInvested = new Money();
    }

    /** {@inheritdoc} */
    public function getMinInvestmentValue(): float
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @Groups({"read", "list"})
     * */
    public function getMaxInvestmentValue(): float
    {
        return $this->purchasePrice->getAmount() - $this->totalInvested->getAmount();
    }

    /** {@inheritdoc} */
    public function getTotalInvested(): Money
    {
        return $this->totalInvested;
    }

    /** {@inheritdoc} */
    public function setTotalInvested(Money $total): self
    {
        $this->totalInvested = $total;

        return $this;
    }

    public function getProjectStatus()
    {
        return $this->projectStatus;
    }

    public function setProjectStatus($projectStatus): self
    {
        $this->projectStatus = $projectStatus;

        return $this;
    }

    public function getPurchasePrice(): Money
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(Money $purchasePrice): self
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getSalePriceProjection(): Money
    {
        return $this->salePriceProjection;
    }

    public function setSalePriceProjection(Money $salePriceProjection): self
    {
        $this->salePriceProjection = $salePriceProjection;

        return $this;
    }

    public function getProjectType()
    {
        return $this->projectType;
    }

    public function setProjectType($projectType): self
    {
        $this->projectType = $projectType;

        return $this;
    }

    public function getSaleRealPrice(): Money
    {
        return $this->saleRealPrice;
    }

    public function setSaleRealPrice(Money $saleRealPrice): self
    {
        $this->saleRealPrice = $saleRealPrice;

        return $this;
    }

    public function getPaymentsProcessedAt(): ?\DateTimeInterface
    {
        return $this->paymentsProcessedAt;
    }

    public function setPaymentsProcessedAt(?\DateTimeInterface $paymentsProcessedAt): self
    {
        $this->paymentsProcessedAt = $paymentsProcessedAt;

        return $this;
    }

    public function getConstructionCost(): Money
    {
        return $this->constructionCost;
    }

    public function setConstructionCost(Money $constructionCost): self
    {
        $this->constructionCost = $constructionCost;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(?int $bedrooms): self
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(?int $bathrooms): self
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    public function getEngineer(): ?Engineer
    {
        return $this->engineer;
    }

    public function setEngineer(?Engineer $engineer): self
    {
        $this->engineer = $engineer;

        return $this;
    }

    public function getBroker(): ?Broker
    {
        return $this->broker;
    }

    public function setBroker(?Broker $broker): self
    {
        $this->broker = $broker;

        return $this;
    }

    public function getContractor(): ?Contractor
    {
        return $this->contractor;
    }

    public function setContractor(?Contractor $contractor): self
    {
        $this->contractor = $contractor;

        return $this;
    }

    public function getEffectiveCost(): float
    {
        $effectiveCost = 0.0;
        if ($this->getPurchasePrice() && $this->getConstructionCost()) {
            $effectiveCost = $this->getPurchasePrice()->getAmount() + $this->getConstructionCost()->getAmount();
        }

        return $effectiveCost;
    }

    /**
     * @Groups({"read", "list"})
     */
    public function getRoi()
    {
        $roi = 0;
        if ($this->getSalePriceProjection()) {
            $amount = $this->getSalePriceProjection()->getAmount();
            $effectiveCost = $this->getEffectiveCost();
            if ($amount > 0) {
                $roi = ($amount - $effectiveCost) / $amount;
            }
        }

        return $roi;
    }
}
