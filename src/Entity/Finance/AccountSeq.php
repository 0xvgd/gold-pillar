<?php

namespace App\Entity\Finance;

use App\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/*
 * TODO turn company relationship not null
 */

/**
 * @ORM\Entity(repositoryClass="App\Repository\Finance\AccountSeqRepository")
 */
class AccountSeq
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
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     */
    private $company;

    public function __construct(?Company $company)
    {
        $this->company = $company;
        $this->value = 1;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function getNextValue(): ?int
    {
        return ++$this->value;
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

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
