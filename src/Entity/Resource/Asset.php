<?php

namespace App\Entity\Resource;

use App\Entity\Asset\AssetEquity;
use App\Entity\Money;
use App\Entity\Person\Broker;
use App\Entity\Person\Contractor;
use App\Entity\Person\Engineer;
use App\Enum\AssetStatus;
use App\Enum\AssetType;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="resource_assets")
 * @ORM\EntityListeners({"App\EntityListener\Asset\AssetListener"})
 * @ORM\Entity(repositoryClass="App\Repository\Asset\AssetRepository")
 */
class Asset extends Resource implements InvestiblaInterface
{
    /**
     * @var AssetStatus
     *
     * @ORM\Column(type=AssetStatus::class, length=20)
     * @Groups({"read", "list"})
     */
    private $assetStatus;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="market_value_")
     * @Groups({"read", "list"})
     */
    private $marketValue;

    /**
     * @var AssetType
     *
     * @ORM\Column(type=AssetType::class, length=20)
     * @Groups({"read", "list"})
     */
    private $assetType;

    /**
     * @var Engineer
     *
     * @ORM\ManyToOne(targetEntity=Engineer::class, inversedBy="assets")
     * @Groups({"read"})
     */
    private $engineer;

    /**
     * @var Broker
     *
     * @ORM\ManyToOne(targetEntity=Broker::class, inversedBy="assets")
     * @Groups({"read"})
     */
    private $broker;

    /**
     * @var Contractor
     *
     * @ORM\ManyToOne(targetEntity=Contractor::class, inversedBy="assets")
     * @Groups({"read"})
     */
    private $contractor;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read"})
     */
    private $paymentsProcessedAt;

    /**
     * @var AssetEquity
     *
     * @ORM\ManyToOne(targetEntity=AssetEquity::class)
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"read", "list"})
     */
    private $lastEquity;

    /**
     * @var AssetEquity[]
     *
     * @ORM\OneToMany(targetEntity=AssetEquity::class, cascade="all", mappedBy="asset", fetch="EXTRA_LAZY", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     * @Groups({"read", "list"})
     */
    private $assetEquities;

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
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read", "list"})
     */
    private $leisureArea;

    /**
     * @var DateTimeInterface|null
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"read", "list"})
     */
    private $birthday;

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
        $this->marketValue = new Money();
        $this->totalInvested = new Money();
        $this->assetEquities = new ArrayCollection();
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
        if ($this->lastEquity) {
            // remaining value
            return $this->lastEquity->getPrice()->getAmount() - $this->totalInvested->getAmount();
        }

        return 0;
    }

    public function getDividendBalance(): float
    {
        return $this->getAccount()->getBalance() - $this->totalInvested->getAmount();
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

    public function getAssetStatus()
    {
        return $this->assetStatus;
    }

    public function setAssetStatus($assetStatus): self
    {
        $this->assetStatus = $assetStatus;

        return $this;
    }

    public function getMarketValue(): Money
    {
        return $this->marketValue;
    }

    public function setMarketValue(Money $marketValue): self
    {
        $this->marketValue = $marketValue;

        return $this;
    }

    public function getAssetType()
    {
        return $this->assetType;
    }

    public function setAssetType($assetType): self
    {
        $this->assetType = $assetType;

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

    public function getLeisureArea(): ?bool
    {
        return $this->leisureArea;
    }

    public function setLeisureArea(?bool $leisureArea): self
    {
        $this->leisureArea = $leisureArea;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

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

    /**
     * @return Collection|AssetEquity[]
     */
    public function getAssetEquities(): Collection
    {
        return $this->assetEquities;
    }

    public function addAssetEquity(AssetEquity $assetEquity): self
    {
        if (!$this->assetEquities->contains($assetEquity)) {
            $this->assetEquities[] = $assetEquity;
            $assetEquity->setAsset($this);
        }

        return $this;
    }

    public function removeAssetEquity(AssetEquity $assetEquity): self
    {
        if ($this->assetEquities->contains($assetEquity)) {
            $this->assetEquities->removeElement($assetEquity);
            // set the owning side to null (unless already changed)
            if ($assetEquity->getAsset() === $this) {
                $assetEquity->setAsset(null);
            }
        }

        return $this;
    }

    public function getLastEquity(): ?AssetEquity
    {
        return $this->lastEquity;
    }

    public function setLastEquity(?AssetEquity $lastEquity): self
    {
        $this->lastEquity = $lastEquity;

        return $this;
    }

    /**
     * @Groups({"read", "list"})
     */
    public function getGrossYield()
    {
        return $this->getYearlyIncome()
            ? ($this->getYearlyIncome() / $this->getMarketValue()->getAmount())
            : 0;
    }
}
