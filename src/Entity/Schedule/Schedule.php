<?php

namespace App\Entity\Schedule;

use App\Entity\Person\Agent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="schedules")
 * @ORM\Entity(repositoryClass="App\Repository\Schedule\ScheduleRepository")
 */
class Schedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Agent::class, inversedBy="schedule")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $agent;

    /**
     * @ORM\OneToMany(targetEntity=Shift::class, mappedBy="schedule", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position": "ASC"})
     */
    private $shifts;

    public function __construct()
    {
        $this->shifts = new ArrayCollection();
        $shift = new Shift();
        $shift->setPosition(1);
        $this->addShift($shift);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * @return Collection|Shift[]
     */
    public function getShifts(): Collection
    {
        return $this->shifts;
    }

    public function addShift(Shift $shift): self
    {
        if (!$this->shifts->contains($shift)) {
            $this->shifts[] = $shift;
            $shift->setSchedule($this);
        }

        return $this;
    }

    public function removeShift(Shift $shift): self
    {
        if ($this->shifts->contains($shift)) {
            $this->shifts->removeElement($shift);
            // set the owning side to null (unless already changed)
            if ($shift->getSchedule() === $this) {
                $shift->setSchedule(null);
            }
        }

        return $this;
    }
}
