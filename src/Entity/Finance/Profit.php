<?php

namespace App\Entity\Finance;

use App\Entity\Resource\Resource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="finance_profits")
 */
class Profit
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
     * @var resource
     *
     * @ORM\ManyToOne(targetEntity=Resource::class, inversedBy="dividends")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $resource;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @Groups({"list", "read"})
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"list", "read"})
     */
    private $roi;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"list", "read"})
     */
    private $investorsCount;

    /**
     * @ORM\Column(type="date")
     * @Groups({"list", "read"})
     */
    private $exDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"list", "read"})
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
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

    public function getRoi(): ?float
    {
        return $this->roi;
    }

    public function setRoi(float $roi): self
    {
        $this->roi = $roi;

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

    public function getInvestorsCount(): ?int
    {
        return $this->investorsCount;
    }

    public function setInvestorsCount(int $investorsCount): self
    {
        $this->investorsCount = $investorsCount;

        return $this;
    }

    public function getExDate(): ?\DateTimeInterface
    {
        return $this->exDate;
    }

    public function setExDate(\DateTimeInterface $exDate): self
    {
        $this->exDate = $exDate;

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
