<?php

namespace App\Entity\Finance;

use App\Entity\Resource\Resource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Finance\CommissionRepository")
 * @ORM\Table(name="finance_commissions")
 */
class Commission
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Resource\Resource", inversedBy="commissions")
     * @ORM\JoinColumn(nullable=false)
     * @MaxDepth(1)
     * @Groups({"list"})
     */
    private $resource;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"read", "list"})
     */
    private $refAmount;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"read", "list"})
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Groups({"read", "list"})
     */
    private $fee;
    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read", "list"})
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getRefAmount(): ?string
    {
        return $this->refAmount;
    }

    public function setRefAmount(string $refAmount): self
    {
        $this->refAmount = $refAmount;

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
