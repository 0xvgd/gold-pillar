<?php

namespace App\Entity;

use App\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\EntityListeners({"App\EntityListener\ExecutiveClubListener"})
 * @ORM\Entity(repositoryClass="App\Repository\ExecutiveClubRepository")
 */
class ExecutiveClub
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
     * @ORM\Column(type="string", length=50)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $minSales;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxSales;

    /**
     * @ORM\Column(type="float")
     */
    private $fee;

    /**
     * @ORM\Column(type="float")
     */
    private $distributableValue;

    /**
     * @ORM\Column(type="float")
     */
    private $companyFee;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="executiveClubs")
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $pathLogo;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMinSales(): ?int
    {
        return $this->minSales;
    }

    public function setMinSales(int $minSales): self
    {
        $this->minSales = $minSales;

        return $this;
    }

    public function getMaxSales(): ?int
    {
        return $this->maxSales;
    }

    public function setMaxSales(int $maxSales): self
    {
        $this->maxSales = $maxSales;

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

    public function getDistributableValue(): ?float
    {
        return $this->distributableValue;
    }

    public function setDistributableValue(float $distributableValue): self
    {
        $this->distributableValue = $distributableValue;

        return $this;
    }

    public function getCompanyFee(): ?float
    {
        return $this->companyFee;
    }

    public function setCompanyFee(float $companyFee): self
    {
        $this->companyFee = $companyFee;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPathLogo(): ?string
    {
        return $this->pathLogo;
    }

    public function setPathLogo(?string $pathLogo): self
    {
        $this->pathLogo = $pathLogo;

        return $this;
    }
}
