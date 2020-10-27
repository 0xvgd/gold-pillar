<?php

namespace App\Entity\Schedule;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="schedule_shifts")
 * @ORM\Entity(repositoryClass="App\Repository\Schedule\ShiftRepository")
 */
class Shift
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Schedule::class, inversedBy="shifts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $schedule;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\OneToMany(targetEntity=Day::class, mappedBy="shift", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"weekDay": "ASC"})
     */
    private $days;

    public function __construct()
    {
        $this->days = self::generateDays();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|Day[]
     */
    public function getDays(): Collection
    {
        return $this->days;
    }

    public function addDay(Day $day): self
    {
        if (!$this->days->contains($day)) {
            $this->days[] = $day;
            $day->setShift($this);
        }

        return $this;
    }

    public function removeDay(Day $day): self
    {
        if ($this->days->contains($day)) {
            $this->days->removeElement($day);
            // set the owning side to null (unless already changed)
            if ($day->getShift() === $this) {
                $day->setShift(null);
            }
        }

        return $this;
    }

    public static function generateDays(): ArrayCollection
    {
        $days = new ArrayCollection();
        for ($i = 1; $i <= 7; ++$i) {
            $day = new Day();
            $day
                ->setWeekDay($i)
                ->setEnabled($i < 6);
            $days->add($day);
        }

        return $days;
    }
}
