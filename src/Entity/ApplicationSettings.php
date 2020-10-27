<?php

namespace App\Entity;

use App\Entity\Security\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\EntityListeners()
 * @ORM\Entity()
 */
class ApplicationSettings
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
    private $commissionRate;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="monthly_fee_")
     * @Groups({"read", "list"})
     */
    private $monthlyFee;

    /**
     * @ORM\OneToMany(targetEntity=Plan::class, mappedBy="applicationSettings")
     */
    private $plans;

    /**
     * @ORM\OneToOne(
     *  targetEntity=Application::class,
     *  inversedBy="applicationSettings",
     *  cascade={"persist", "remove"}
     * )
     */
    private $application;

    /**
     * @ORM\OneToMany(targetEntity=ExecutiveClub::class, mappedBy="applicationSettings")
     */
    private $executiveClubs;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $companyFee;

    /**
     * @ORM\OneToMany(targetEntity=InvestorProfit::class, mappedBy="applicationSettings")
     */
    private $investorProfits;

    /**
     * @ORM\OneToMany(targetEntity=AssetEntryFee::class, mappedBy="applicationSettings")
     */
    private $assetEntryFees;

    /**
     * @ORM\OneToMany(targetEntity=AssetInvestmentClass::class, mappedBy="applicationSettings")
     */
    private $assetInvestmentClasses;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
        $this->executiveClubs = new ArrayCollection();
        $this->investorProfits = new ArrayCollection();
        $this->assetEntryFees = new ArrayCollection();
        $this->assetInvestmentClasses = new ArrayCollection();
        $this->monthlyFee = new Money();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getCommissionRate(): ?float
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(?float $commissionRate): self
    {
        $this->commissionRate = $commissionRate;

        return $this;
    }

    public function getMonthlyFee(): Money
    {
        return $this->monthlyFee;
    }

    public function setMonthlyFee(Money $monthlyFee): self
    {
        $this->monthlyFee = $monthlyFee;

        return $this;
    }

    /**
     * @return Collection|Plan[]
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): self
    {
        if (!$this->plans->contains($plan)) {
            $this->plans[] = $plan;
            $plan->setApplicationSettings($this);
        }

        return $this;
    }

    public function removePlan(Plan $plan): self
    {
        if ($this->plans->contains($plan)) {
            $this->plans->removeElement($plan);
            // set the owning side to null (unless already changed)
            if ($plan->getApplicationSettings() === $this) {
                $plan->setApplicationSettings(null);
            }
        }

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return Collection|ExecutiveClub[]
     */
    public function getExecutiveClubs(): Collection
    {
        return $this->executiveClubs;
    }

    public function addExecutiveClub(ExecutiveClub $executiveClub): self
    {
        if (!$this->executiveClubs->contains($executiveClub)) {
            $this->executiveClubs[] = $executiveClub;
            $executiveClub->setApplicationSettings($this);
        }

        return $this;
    }

    public function removeExecutiveClub(ExecutiveClub $executiveClub): self
    {
        if ($this->executiveClubs->contains($executiveClub)) {
            $this->executiveClubs->removeElement($executiveClub);
            // set the owning side to null (unless already changed)
            if ($executiveClub->getApplicationSettings() === $this) {
                $executiveClub->setApplicationSettings(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ExecutiveClub[]
     */
    public function getAssetInvestmentClasses(): Collection
    {
        return $this->assetInvestmentClasses;
    }

    public function addAssetInvestmentClass(AssetInvestmentClass $assetInvestmentClass): self
    {
        if (!$this->executiveClubs->contains($assetInvestmentClass)) {
            $this->executiveClubs[] = $assetInvestmentClass;
            $assetInvestmentClass->setApplicationSettings($this);
        }

        return $this;
    }

    public function removeAssetInvestmentClass(AssetInvestmentClass $assetInvestmentClass): self
    {
        if ($this->executiveClubs->contains($assetInvestmentClass)) {
            $this->executiveClubs->removeElement($assetInvestmentClass);
            if ($assetInvestmentClass->getApplicationSettings() === $this) {
                $assetInvestmentClass->setApplicationSettings(null);
            }
        }

        return $this;
    }

    public function getCompanyFee(): ?float
    {
        return $this->companyFee;
    }

    public function setCompanyFee(?float $companyFee): self
    {
        $this->companyFee = $companyFee;

        return $this;
    }

    /**
     * @return Collection|InvestorProfit[]
     */
    public function getInvestorProfits(): Collection
    {
        return $this->investorProfits;
    }

    public function addInvestorProfit(InvestorProfit $investorProfit): self
    {
        if (!$this->investorProfits->contains($investorProfit)) {
            $this->investorProfits[] = $investorProfit;
            $investorProfit->setApplicationSettings($this);
        }

        return $this;
    }

    public function removeInvestorProfit(InvestorProfit $investorProfit): self
    {
        if ($this->investorProfits->contains($investorProfit)) {
            $this->investorProfits->removeElement($investorProfit);
            // set the owning side to null (unless already changed)
            if ($investorProfit->getApplicationSettings() === $this) {
                $investorProfit->setApplicationSettings(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AssetEntryFee[]
     */
    public function getAssetEntryFees(): Collection
    {
        return $this->assetEntryFees;
    }

    public function addAssetEntryFee(AssetEntryFee $assetEntryFee): self
    {
        if (!$this->assetEntryFees->contains($assetEntryFee)) {
            $this->assetEntryFees[] = $assetEntryFee;
            $assetEntryFee->setApplicationSettings($this);
        }

        return $this;
    }

    public function removeAssetEntryFee(AssetEntryFee $assetEntryFee): self
    {
        if ($this->assetEntryFees->contains($assetEntryFee)) {
            $this->assetEntryFees->removeElement($assetEntryFee);
            // set the owning side to null (unless already changed)
            if ($assetEntryFee->getApplicationSettings() === $this) {
                $assetEntryFee->setApplicationSettings(null);
            }
        }

        return $this;
    }
}
