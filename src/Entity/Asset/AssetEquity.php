<?php

namespace App\Entity\Asset;

use App\Entity\Money;
use App\Entity\Resource\Asset;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Asset\AssetEquityRepository")
 */
class AssetEquity
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
     * @ORM\Column(type="integer")
     */
    private $monthRef;

    /**
     * @ORM\Column(type="integer")
     */
    private $yearRef;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="price_")
     * @Groups({"read", "list"})
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Asset::class, inversedBy="assetEquities")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $asset;

    public function __construct()
    {
        $this->price = new Money();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getMonthRef(): ?int
    {
        return $this->monthRef;
    }

    public function setMonthRef(int $monthRef): self
    {
        $this->monthRef = $monthRef;

        return $this;
    }

    public function getYearRef(): ?int
    {
        return $this->yearRef;
    }

    public function setYearRef(int $yearRef): self
    {
        $this->yearRef = $yearRef;

        return $this;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): self
    {
        $this->asset = $asset;

        return $this;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): self
    {
        $this->price = $price;

        return $this;
    }
}
