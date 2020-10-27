<?php

namespace App\Entity\Company;

use App\Entity\ExecutiveClub;
use App\Entity\Finance\Account;
use App\Entity\InvestorProfit;
use App\Entity\Plan;
use App\Entity\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="companies")
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    use Timestampable;

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"read", "list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "list"})
     */
    private $name;

    /**
     * @var Account
     *
     * @ORM\OneToOne(targetEntity=Account::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $account;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read", "list"})
     */
    private $defaultCompany;

    /**
     * @ORM\Embedded(class=Settings::class, columnPrefix="settings_")
     * @Groups({"read", "list"})
     */
    protected $settings;

    /**
     * @ORM\OneToMany(targetEntity=InvestorProfit::class, mappedBy="company")
     * @Groups({"read", "list"})
     */
    private $investorProfits;

    /**
     * @ORM\OneToMany(targetEntity=App\Entity\ExecutiveClub::class, mappedBy="company")
     * @Groups({"read", "list"})
     */
    private $executiveClubs;

    /**
     * @ORM\OneToMany(targetEntity=App\Entity\Plan::class, mappedBy="company")
     */
    private $plans;

    public function __construct()
    {
        $this->settings = new Settings();
        $this->investorProfits = new ArrayCollection();
        $this->executiveClubs = new ArrayCollection();
        $this->plans = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getDefaultCompany(): ?bool
    {
        return $this->defaultCompany;
    }

    public function setDefaultCompany(?bool $defaultCompany): self
    {
        $this->defaultCompany = $defaultCompany;

        return $this;
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function setAddress(Settings $settings): self
    {
        $this->settings = $settings;

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
            $investorProfit->setCompany($this);
        }

        return $this;
    }

    public function removeInvestorProfit(InvestorProfit $investorProfit): self
    {
        if ($this->investorProfits->contains($investorProfit)) {
            $this->investorProfits->removeElement($investorProfit);

            if ($investorProfit->getCompany() === $this) {
                $investorProfit->setCompany(null);
            }
        }

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
            $executiveClub->setCompany($this);
        }

        return $this;
    }

    public function removeExecutiveClub(ExecutiveClub $executiveClub): self
    {
        if ($this->executiveClubs->contains($executiveClub)) {
            $this->executiveClubs->removeElement($executiveClub);
            if ($executiveClub->getCompany() === $this) {
                $executiveClub->setCompany(null);
            }
        }

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
}
