<?php

namespace App\Entity\Finance;

use App\Entity\Resource\Resource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 */
class Payment
{
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
     * @ORM\Column(type="datetime")
     * @Groups({"list", "read"})
     */
    private $createdAt;

    /**
     * @var resource
     *
     * @ORM\ManyToOne(targetEntity=Resource::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $resource;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     * @Groups({"list", "read", "write"})
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @Groups({"list", "read", "write"})
     */
    private $note;

    /**
     * @var Recurring
     *
     * @ORM\ManyToOne(targetEntity=Recurring::class)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Groups({"list", "read"})
     */
    private $recurring;

    /**
     * @var Transaction
     *
     * @ORM\ManyToOne(targetEntity=Transaction::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"list", "read"})
     */
    private $transaction;

    public function __construct(Resource $resource)
    {
        $this->createdAt = new DateTime();
        $this->resource = $resource;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function isRecurring(): ?bool
    {
        return (bool) $this->recurring;
    }

    public function getRecurring(): ?Recurring
    {
        return $this->recurring;
    }

    public function setRecurring(?Recurring $recurring): self
    {
        $this->recurring = $recurring;

        return $this;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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
}
