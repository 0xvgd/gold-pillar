<?php

namespace App\Entity\Resource;

use App\Entity\Money;
use App\Enum\PropertyStatus;
use App\Enum\PropertyType;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="resource_properties")
 * @ORM\EntityListeners({"App\EntityListener\PropertyListener"})
 * @ORM\Entity(repositoryClass="App\Repository\Sales\PropertyRepository")
 */
class Property extends Resource implements OfferableInterface
{
    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class, columnPrefix="price_")
     * @Groups({"read", "list"})
     */
    private $price;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "list"})
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"read", "list"})
     */
    private $commissionRate;

    /**
     * @var PropertyType
     *
     * @ORM\Column(type=PropertyType::class, length=20)
     * @Groups({"read", "list"})
     */
    private $propertyType;

    /**
     * @var PropertyStatus
     *
     * @ORM\Column(type=PropertyStatus::class, length=20)
     * @Groups({"read", "list"})
     */
    private $propertyStatus;

    /**
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
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"read", "list"})
     */
    private $squareFoot;

    public function __construct()
    {
        parent::__construct();
        $this->price = new Money();
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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getCommissionRate(): ?string
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(?string $commissionRate): self
    {
        $this->commissionRate = $commissionRate;

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

    public function getPropertyStatus()
    {
        return $this->propertyStatus;
    }

    public function setPropertyStatus($propertyStatus): self
    {
        $this->propertyStatus = $propertyStatus;

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

    public function getSquareFoot(): ?float
    {
        return $this->squareFoot;
    }

    public function setSquareFoot(?float $squareFoot): self
    {
        $this->squareFoot = $squareFoot;

        return $this;
    }

    public function getOfferPrice(): Money
    {
        return $this->price;
    }
}
