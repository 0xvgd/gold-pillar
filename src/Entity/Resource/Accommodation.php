<?php

namespace App\Entity\Resource;

use App\Entity\Money;
use App\Entity\Reserve\Reserve;
use App\Enum\AccommodationStatus;
use App\Enum\PropertyType;
use App\Enum\RentingPlan;
use App\Enum\TermType;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="resource_accommodations")
 * @ORM\EntityListeners({"App\EntityListener\Renting\AccommodationListener"})
 * @ORM\Entity(repositoryClass="App\Repository\Renting\AccommodationRepository")
 */
class Accommodation extends Resource implements OfferableInterface
{
    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $deadline;

    /**
     * @var PropertyType
     *
     * @ORM\Column(type=PropertyType::class, length=20)
     * @Groups({"read", "list"})
     */
    private $propertyType;

    /**
     * @var Status
     *
     * @ORM\Column(type=AccommodationStatus::class, length=20)
     * @Groups({"read", "list"})
     */
    private $status;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $soldAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read", "list"})
     */
    private $bedrooms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read", "list"})
     */
    private $bathrooms;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"read", "list"})
     */
    private $squareFoot;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="rent_")
     * @Groups({"read", "list"})
     */
    private $rent;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="deposit_")
     * @Groups({"read", "list"})
     */
    private $deposit;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read", "list"})
     */
    private $terms;

    /**
     * @var string
     *
     * @ORM\Column(type=TermType::class, nullable=true)
     * @Groups({"read", "list"})
     */
    private $termType;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read", "list"})
     */
    private $letAvailableFor;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $availableAt;

    /**
     * @ORM\OneToMany(targetEntity=Reserve::class, mappedBy="accommodation")
     */
    private $reserves;

    /**
     * @ORM\Column(type=RentingPlan::class, nullable=true)
     */
    private $plan;

    public function __construct()
    {
        parent::__construct();

        $this->rent = new Money();
        $this->deposit = new Money();
        $this->reserves = new ArrayCollection();
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getPropertyType()
    {
        return $this->propertyType;
    }

    public function setPropertyType($propertyType): self
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSoldAt(): ?\DateTimeInterface
    {
        return $this->soldAt;
    }

    public function setSoldAt(?\DateTimeInterface $soldAt): self
    {
        $this->soldAt = $soldAt;

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

    public function getSquareFoot(): ?string
    {
        return $this->squareFoot;
    }

    public function setSquareFoot(?string $squareFoot): self
    {
        $this->squareFoot = $squareFoot;

        return $this;
    }

    public function getRent(): Money
    {
        return $this->rent;
    }

    public function setRent(Money $rent): self
    {
        $this->rent = $rent;

        return $this;
    }

    public function getDeposit(): Money
    {
        return $this->deposit;
    }

    public function setDeposit(Money $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    public function getTerms(): ?int
    {
        return $this->terms;
    }

    public function setTerms(?int $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    public function getTermType(): ?TermType
    {
        return $this->termType;
    }

    public function setTermType(?TermType $termType): self
    {
        $this->termType = $termType;

        return $this;
    }

    public function getLetAvailableFor(): ?int
    {
        return $this->letAvailableFor;
    }

    public function setLetAvailableFor(?int $letAvailableFor): self
    {
        $this->letAvailableFor = $letAvailableFor;

        return $this;
    }

    public function getAvailableAt(): ?\DateTimeInterface
    {
        return $this->availableAt;
    }

    public function setAvailableAt(?\DateTimeInterface $availableAt): self
    {
        $this->availableAt = $availableAt;

        return $this;
    }

    public function getOfferPrice(): Money
    {
        return $this->getRent();
    }

    /**
     * @return Collection|Reserve[]
     */
    public function getReserves(): Collection
    {
        return $this->reserves;
    }

    public function getPlan(): ?RentingPlan
    {
        return $this->plan;
    }

    public function setPlan(?RentingPlan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }
}
