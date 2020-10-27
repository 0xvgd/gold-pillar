<?php

namespace App\Entity\Person;

use App\Entity\Negotiation\Offer;
use App\Entity\Resource\Resource;
use App\Entity\Schedule\Schedule;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="person_agents")
 * @ORM\Entity()
 */
class Agent extends Person
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "list"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Resource::class, mappedBy="agent")
     * @Groups({"read"})
     */
    private $resources;

    /**
     * @ORM\OneToMany(targetEntity=Offer::class, mappedBy="agent")
     */
    private $offers;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Schedule\Schedule", mappedBy="agent", cascade={"persist", "remove"})
     */
    private $schedule;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"read", "list"})
     */
    private $birthDate;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"read", "list"})
     */
    private $stars;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"read", "list"})
     */
    private $experience;

    public function __construct()
    {
        $this->stars = 0;
        $this->resources = new ArrayCollection();
        $this->offers = new ArrayCollection();
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
     * @return Collection|resource[]
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): self
    {
        if (!$this->resources->contains($resource)) {
            $this->resources[] = $resource;
            $resource->setAgent($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->contains($resource)) {
            $this->resources->removeElement($resource);
            // set the owning side to null (unless already changed)
            if ($resource->getAgent() === $this) {
                $resource->setAgent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Offer[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(Schedule $schedule): self
    {
        $this->schedule = $schedule;

        // set the owning side of the relation if necessary
        if ($schedule->getAgent() !== $this) {
            $schedule->setAgent($this);
        }

        return $this;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setAgent($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            // set the owning side to null (unless already changed)
            if ($offer->getAgent() === $this) {
                $offer->setAgent(null);
            }
        }

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getStars(): ?float
    {
        return $this->stars;
    }

    public function setStars(?float $stars): self
    {
        $this->stars = $stars;

        return $this;
    }

    public function getExperience(): ?\DateTimeInterface
    {
        return $this->experience;
    }

    public function setExperience(?\DateTimeInterface $experience): self
    {
        $this->experience = $experience;

        return $this;
    }
}
