<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Embeddable
 */
class Address
{
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"read", "list"})
     */
    private $addressLine1;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"read", "list"})
     */
    private $addressLine2;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups({"read", "list"})
     */
    private $town;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups({"read", "list"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     * @Groups({"read", "list"})
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"read", "list"})
     */
    private $country;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     * @Groups({"read", "list"})
     */
    private $lat;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     * @Groups({"read", "list"})
     */
    private $lng;

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setPostcode($postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function setAddressLine1($addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function setAddressLine2($addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function setTown($town): self
    {
        $this->town = $town;

        return $this;
    }

    public function setCity($city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setCountry($country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getDescription()
    {
        $address = $this->getAddressLine1();

        if ($this->getAddressLine2()) {
            $address .= ", {$this->getAddressLine2()}";
        }
        if ($this->getCity()) {
            $address .= ", {$this->getCity()}";
        }
        if ($this->getPostcode()) {
            $address .= ", {$this->getPostcode()}";
        }

        return $address;
    }

    public function __toString()
    {
        return $this->getDescription();
    }
}
