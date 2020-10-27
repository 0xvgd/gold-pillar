<?php

namespace App\Entity\Person;

use App\Entity\Finance\Investment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="person_investors")
 * @ORM\Entity()
 */
class Investor extends Person
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "list"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Investment::class, mappedBy="investor")
     */
    private $investments;

    public function __construct()
    {
        $this->investments = new ArrayCollection();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Investment[]
     */
    public function getInvestments(): Collection
    {
        return $this->investments;
    }

    public function addInvestment(Investment $investment): self
    {
        if (!$this->investments->contains($investment)) {
            $this->investments[] = $investment;
            $investment->setInvestor($this);
        }

        return $this;
    }

    public function removeInvestment(Investment $investment): self
    {
        if ($this->investments->contains($investment)) {
            $this->investments->removeElement($investment);
            // set the owning side to null (unless already changed)
            if ($investment->getInvestor() === $this) {
                $investment->setInvestor(null);
            }
        }

        return $this;
    }
}
