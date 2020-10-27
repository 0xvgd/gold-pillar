<?php

namespace App\Entity\Person;

use App\Entity\Negotiation\Offer;
use App\Entity\Reserve\Reserve;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="person_tenants")
 * @ORM\Entity(repositoryClass="App\Repository\Person\TenantRepository")
 */
class Tenant extends Person
{
    /**
     * @ORM\OneToMany(targetEntity=Offer::class, mappedBy="tenant")
     */
    private $offers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reserve\Reserve", mappedBy="tenant")
     */
    private $reserves;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
        $this->reserves = new ArrayCollection();
    }

    /**
     * @return Collection|Offer[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    /**
     * @return Collection|Reserve[]
     */
    public function getReserves(): Collection
    {
        return $this->reserves;
    }

    public function addReserve(Reserve $reserve): self
    {
        if (!$this->reserves->contains($reserve)) {
            $this->reserves[] = $reserve;
            $reserve->setTenant($this);
        }

        return $this;
    }

    public function removeReserve(Reserve $reserve): self
    {
        if ($this->reserves->contains($reserve)) {
            $this->reserves->removeElement($reserve);
            // set the owning side to null (unless already changed)
            if ($reserve->getTenant() === $this) {
                $reserve->setTenant(null);
            }
        }

        return $this;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setTenant($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            // set the owning side to null (unless already changed)
            if ($offer->getTenant() === $this) {
                $offer->setTenant(null);
            }
        }

        return $this;
    }
}
