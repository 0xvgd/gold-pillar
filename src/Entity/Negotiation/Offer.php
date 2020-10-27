<?php

namespace App\Entity\Negotiation;

use App\Entity\Money;
use App\Entity\Person\Agent;
use App\Entity\Person\Tenant;
use App\Entity\Resource\Resource;
use App\Entity\Timestampable;
use App\Enum\OfferStatus;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Negotiation\OfferRepository")
 */
class Offer
{
    use Timestampable;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Resource\Resource", inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resource;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity=Agent::class, inversedBy="offers")
     * @Groups({"read"})
     */
    private $agent;

    /**
     * @ORM\ManyToOne(targetEntity=Tenant::class, inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tenant;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="original_price_")
     * @Groups({"read", "list"})
     */
    private $originalPrice;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"read", "list"})
     */
    private $offerValue;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"read", "list"})
     */
    private $counterOfferValue;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"read", "list"})
     */
    private $agreedValue;

    /**
     * @var OfferStatus
     *
     * @ORM\Column(type=OfferStatus::class, length=50)
     * @Groups({"read", "list"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $declinedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $offerAcceptedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $offerDeclinedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $counterOfferAcceptedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $counterOfferDeclinedAt;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"list", "read"})
     */
    private $peopleCount = 0;

    public function __construct()
    {
        $this->originalPrice = new Money();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): self
    {
        $this->resource = $resource;

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

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getOriginalPrice(): ?Money
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?Money $originalPrice): self
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    public function getOfferValue(): ?string
    {
        return $this->offerValue;
    }

    public function setOfferValue(?string $offerValue): self
    {
        $this->offerValue = $offerValue;

        return $this;
    }

    public function getCounterOfferValue(): ?string
    {
        return $this->counterOfferValue;
    }

    public function setCounterOfferValue(?string $counterOfferValue): self
    {
        $this->counterOfferValue = $counterOfferValue;

        return $this;
    }

    public function getAgreedValue(): ?string
    {
        return $this->agreedValue;
    }

    public function setAgreedValue(?string $agreedValue): self
    {
        $this->agreedValue = $agreedValue;

        return $this;
    }

    public function getStatus(): OfferStatus
    {
        return $this->status;
    }

    public function setStatus(OfferStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDeclinedAt(): ?\DateTimeInterface
    {
        return $this->declinedAt;
    }

    public function setDeclinedAt(?\DateTimeInterface $declinedAt): self
    {
        $this->declinedAt = $declinedAt;

        return $this;
    }

    public function getOfferAcceptedAt(): ?\DateTimeInterface
    {
        return $this->offerAcceptedAt;
    }

    public function setOfferAcceptedAt(?\DateTimeInterface $offerAcceptedAt): self
    {
        $this->offerAcceptedAt = $offerAcceptedAt;

        return $this;
    }

    public function getOfferDeclinedAt(): ?\DateTimeInterface
    {
        return $this->offerDeclinedAt;
    }

    public function setOfferDeclinedAt(?\DateTimeInterface $offerDeclinedAt): self
    {
        $this->offerDeclinedAt = $offerDeclinedAt;

        return $this;
    }

    public function getCounterOfferAcceptedAt(): ?\DateTimeInterface
    {
        return $this->counterOfferAcceptedAt;
    }

    public function setCounterOfferAcceptedAt(?\DateTimeInterface $counterOfferAcceptedAt): self
    {
        $this->counterOfferAcceptedAt = $counterOfferAcceptedAt;

        return $this;
    }

    public function getCounterOfferDeclinedAt(): ?\DateTimeInterface
    {
        return $this->counterOfferDeclinedAt;
    }

    public function setCounterOfferDeclinedAt(?\DateTimeInterface $counterOfferDeclinedAt): self
    {
        $this->counterOfferDeclinedAt = $counterOfferDeclinedAt;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

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
}
