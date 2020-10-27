<?php

namespace App\Entity\Reserve;

use App\Entity\Money;
use App\Entity\Person\Tenant;
use App\Entity\Resource\Accommodation;
use App\Entity\Timestampable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="reserves")
 * @ORM\Entity(repositoryClass="App\Repository\Reserve\ReserveRepository")
 */
class Reserve
{
    use Timestampable;

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"list", "read"})
     */
    private $id;

    /**
     * @ORM\Embedded(class=Money::class, columnPrefix="fee_")
     * @Groups({"list", "read"})
     */
    private $fee;

    /**
     * @ORM\ManyToOne(targetEntity=Accommodation::class, inversedBy="reserves")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"list", "read"})
     */
    private $accommodation;

    /**
     * @ORM\OneToMany(targetEntity=Person::class, mappedBy="reserve", cascade={"persist"}, orphanRemoval=true)
     * @Groups({"read"})
     */
    private $people;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"list", "read"})
     */
    private $peopleCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"list", "read"})
     */
    private $processedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="reserves")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list", "read"})
     */
    private $tenant;

    /**
     * @ORM\Column(type="string", length=12)
     * @Groups({"list", "read"})
     */
    private $referenceCode;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->people = new ArrayCollection();
        $this->fee = new Money();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getFee(): ?Money
    {
        return $this->fee;
    }

    public function setFee(Money $fee): self
    {
        $this->fee = $fee;

        return $this;
    }

    public function getAccommodation(): ?Accommodation
    {
        return $this->accommodation;
    }

    public function setAccommodation(?Accommodation $accommodation): self
    {
        $this->accommodation = $accommodation;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeInterface
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeInterface $processedAt): self
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $this->peopleCount = count($this->people);
            $person->setReserve($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->contains($person)) {
            $this->people->removeElement($person);
            // set the owning side to null (unless already changed)
            if ($person->getReserve() === $this) {
                $person->setReserve(null);
            }
            $this->peopleCount = count($this->people);
        }

        return $this;
    }

    public function getPeopleCount(): ?int
    {
        return $this->peopleCount;
    }

    public function setPeopleCount(int $peopleCount): self
    {
        $this->peopleCount = $peopleCount;

        return $this;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getReferenceCode(): ?string
    {
        return $this->referenceCode;
    }

    public function setReferenceCode(string $referenceCode): self
    {
        $this->referenceCode = $referenceCode;

        return $this;
    }
}
