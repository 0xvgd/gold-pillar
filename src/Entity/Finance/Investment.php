<?php

namespace App\Entity\Finance;

use App\Entity\Person\Investor;
use App\Entity\Resource\InvestiblaInterface;
use App\Entity\Resource\Resource;
use App\Enum\InvestmentStatus;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="finance_investments")
 * @ORM\Entity()
 */
class Investment
{
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
     * @var Transaction
     *
     * @ORM\ManyToOne(targetEntity=Transaction::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"read", "list"})
     */
    private $transaction;

    /**
     * @var InvestmentStatus
     *
     * @ORM\Column(type=InvestmentStatus::class)
     * @Groups({"read", "list"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read", "list"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $approvedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $deniedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Investor::class, inversedBy="investments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read"})
     */
    private $investor;

    /**
     * @ORM\ManyToOne(targetEntity=Resource::class, inversedBy="investments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read", "list"})
     */
    private $resource;

    /**
     * @ORM\Column(type="string", length=12)
     * @Groups({"list", "read"})
     */
    private $referenceCode;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Groups({"read", "list"})
     */
    private $fee;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Groups({"read", "list"})
     */
    private $amount;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->status = InvestmentStatus::WAITING();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getStatus(): InvestmentStatus
    {
        return $this->status;
    }

    public function setStatus(InvestmentStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getApprovedAt(): ?\DateTimeInterface
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?\DateTimeInterface $approvedAt): self
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    public function getDeniedAt(): ?\DateTimeInterface
    {
        return $this->deniedAt;
    }

    public function setDeniedAt(?\DateTimeInterface $deniedAt): self
    {
        $this->deniedAt = $deniedAt;

        return $this;
    }

    public function isApproved(): bool
    {
        return $this->status->equals(InvestmentStatus::APPROVED());
    }

    public function isWaiting(): bool
    {
        return $this->status->equals(InvestmentStatus::WAITING());
    }

    public function isDenied(): bool
    {
        return $this->status->equals(InvestmentStatus::DENIED());
    }

    public function getInvestor(): ?Investor
    {
        return $this->investor;
    }

    public function setInvestor(?Investor $investor): self
    {
        $this->investor = $investor;

        return $this;
    }

    /**
     * @return InvestiblaInterface
     */
    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function getFee(): ?float
    {
        return $this->fee;
    }

    public function setFee(float $fee): self
    {
        $this->fee = $fee;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

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
