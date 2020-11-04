<?php

namespace App\Entity\Resource;

use App\Entity\Address;
use App\Entity\Company\Company;
use App\Entity\Document;
use App\Entity\Finance\Account;
use App\Entity\Finance\Commission;
use App\Entity\Finance\Dividend;
use App\Entity\Finance\ExpensePayment;
use App\Entity\Finance\IncomePayment;
use App\Entity\Finance\Investment;
use App\Entity\Finance\RecurringExpense;
use App\Entity\Finance\RecurringIncome;
use App\Entity\Negotiation\Offer;
use App\Entity\Person\Agent;
use App\Entity\Security\User;
use App\Entity\Timestampable;
use App\Enum\PostStatus;
use App\Enum\RemovalReason;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hashids\Hashids;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(name="resources")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "asset"=Asset::class,
 *     "project"=Project::class,
 *     "property"=Property::class,
 *     "accommodation"=Accommodation::class
 * })
 */
abstract class Resource implements ResourceInterface
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var PostStatus
     *
     * @ORM\Column(type=PostStatus::class, length=20)
     * @Groups({"read", "list"})
     */
    private $postStatus;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"read", "list"})
     */
    private $tag;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     * @Groups({"read", "list"})
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "list"})
     */
    private $hits = 0;

    /**
     * @var Account
     *
     * @ORM\OneToOne(targetEntity=Account::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $account;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "list"})
     */
    private $mainPhoto;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=false)
     */
    private $photos = [];

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=false)
     */
    private $floorplans = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "list"})
     */
    private $youtubeUrl;

    /**
     * @ORM\Embedded(class=Address::class, columnPrefix="addr_")
     * @Groups({"read", "list"})
     */
    protected $address;

    /**
     * @var RemovalReason
     *
     * @ORM\Column(type=RemovalReason::class, length=20, nullable=true)
     * @Groups({"read"})
     */
    private $removalReason;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Groups({"read"})
     */
    private $removalDetails;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity=Agent::class, inversedBy="resources")
     * @Groups({"read"})
     */
    private $agent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "list"})
     */
    private $referenceCode;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"read", "list"})
     */
    private $owner;

    /**
     * @var Collection|Offer[]
     *
     * @ORM\OneToMany(targetEntity=Offer::class, mappedBy="resource")
     */
    private $offers;

    /**
     * @var Collection|Investment[]
     *
     * @ORM\OneToMany(targetEntity=Investment::class, mappedBy="resource")
     */
    private $investments;

    /**
     * @var Collection|RecurringIncome[]
     *
     * @ORM\OneToMany(targetEntity=RecurringIncome::class, mappedBy="resource", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $recurringIncomes;

    /**
     * @var Collection|RecurringExpense[]
     *
     * @ORM\OneToMany(targetEntity=RecurringExpense::class, mappedBy="resource", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $recurringExpenses;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     * @Groups({"read", "list"})
     */
    private $yearlyIncome;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     * @Groups({"read", "list"})
     */
    private $monthlyIncome;

    /**
     * @var Collection|IncomePayment[]
     *
     * @ORM\OneToMany(targetEntity=IncomePayment::class, mappedBy="resource")
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $incomes;

    /**
     * @var Collection|ExpensePayment[]
     *
     * @ORM\OneToMany(targetEntity=ExpensePayment::class, mappedBy="resource")
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $expenses;

    /**
     * @var Collection|Dividend[]
     *
     * @ORM\OneToMany(targetEntity=Dividend::class, mappedBy="resource")
     */
    private $dividends;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity=Company::class)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Finance\Commission", mappedBy="resource")
     */
    private $commissions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="resource", cascade={"persist"}, orphanRemoval=true)
     */
    private $documents;

    public function __construct()
    {
        $this->address = new Address();
        $this->offers = new ArrayCollection();
        $this->investments = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->recurringIncomes = new ArrayCollection();
        $this->recurringExpenses = new ArrayCollection();
        $this->dividends = new ArrayCollection();
        $this->commissions = new ArrayCollection();
        $this->documents = new ArrayCollection();
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

    public function getHash()
    {
        $hashids = new Hashids(get_class($this), 12);

        return $hashids->encode($this->getId());
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

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

    public function getPostStatus()
    {
        return $this->postStatus;
    }

    public function setPostStatus($postStatus): self
    {
        $this->postStatus = $postStatus;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    public function setHits(int $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    public function getMainPhoto(): ?string
    {
        return $this->mainPhoto;
    }

    public function setMainPhoto(?string $mainPhoto): self
    {
        $this->mainPhoto = $mainPhoto;

        return $this;
    }

    public function getPhotos(): ?array
    {
        return $this->photos;
    }

    public function setPhotos(array $photos): self
    {
        $this->photos = $photos;

        return $this;
    }

    public function getFloorplans(): ?array
    {
        return $this->floorplans;
    }

    public function setFloorplans(array $floorplans): self
    {
        $this->floorplans = $floorplans;

        return $this;
    }

    public function getYoutubeUrl(): ?string
    {
        return $this->youtubeUrl;
    }

    public function setYoutubeUrl(?string $youtubeUrl): self
    {
        $this->youtubeUrl = $youtubeUrl;

        return $this;
    }

    public function getRemovalReason()
    {
        return $this->removalReason;
    }

    public function setRemovalReason($removalReason): self
    {
        $this->removalReason = $removalReason;

        return $this;
    }

    public function getRemovalDetails(): ?string
    {
        return $this->removalDetails;
    }

    public function setRemovalDetails(?string $removalDetails): self
    {
        $this->removalDetails = $removalDetails;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

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

    /**
     * @return Collection|Offer[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setResource($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            // set the owning side to null (unless already changed)
            if ($offer->getResource() === $this) {
                $offer->setResource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Investment[]
     */
    public function getInvestments(): Collection
    {
        return $this->investments;
    }

    /**
     * @return Collection|RecurringExpense[]
     */
    public function getRecurringExpenses(): Collection
    {
        return $this->recurringExpenses;
    }

    /**
     * @return Collection|RecurringIncome[]
     */
    public function getRecurringIncomes(): Collection
    {
        return $this->recurringIncomes;
    }

    /**
     * @return Collection|ExpensePayment[]
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    /**
     * @return Collection|IncomePayment[]
     */
    public function getIncomes(): Collection
    {
        return $this->incomes;
    }

    /**
     * @return Collection|Dividend[]
     */
    public function getDividends(): Collection
    {
        return $this->dividends;
    }

    public function getYearlyIncome(): ?string
    {
        return $this->yearlyIncome;
    }

    public function setYearlyIncome(?string $yearlyIncome): self
    {
        $this->yearlyIncome = $yearlyIncome;

        return $this;
    }

    public function getMonthlyIncome(): ?string
    {
        return $this->monthlyIncome;
    }

    public function setMonthlyIncome(?string $monthlyIncome): self
    {
        $this->monthlyIncome = $monthlyIncome;

        return $this;
    }

    /**
     * @return Collection|Commission[]
     */
    public function getCommissions(): Collection
    {
        return $this->commissions;
    }

    public function addCommission(Commission $commission): self
    {
        if (!$this->commissions->contains($commission)) {
            $this->commissions[] = $commission;
            $commission->setResource($this);
        }

        return $this;
    }

    public function removeCommission(Commission $commission): self
    {
        if ($this->commissions->contains($commission)) {
            $this->commissions->removeElement($commission);
            // set the owning side to null (unless already changed)
            if ($commission->getResource() === $this) {
                $commission->setResource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setResource($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getResource() === $this) {
                $document->setResource(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of referenceCode.
     */
    public function getReferenceCode()
    {
        return $this->referenceCode;
    }

    /**
     * Set the value of referenceCode.
     *
     * @return self
     */
    public function setReferenceCode($referenceCode)
    {
        $this->referenceCode = $referenceCode;

        return $this;
    }
}
