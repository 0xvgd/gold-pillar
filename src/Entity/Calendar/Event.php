<?php

namespace App\Entity\Calendar;

use App\Entity\Security\User;
use App\Entity\Timestampable;
use App\Enum\CalendarEventType;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="calendar_events")
 * @ORM\Entity(repositoryClass="App\Repository\Calendar\EventRepository")
 */
class Event
{
    use Timestampable;

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"list", "read"})
     */
    private $id;

    /**
     * @ORM\Column(type=CalendarEventType::class)
     * @Groups({"list", "read"})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150)
     * @Groups({"list", "read"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"list", "read"})
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read"})
     */
    private $owner;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="date")
     * @Groups({"list", "read"})
     */
    private $date;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="time")
     */
    private $startTime;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="time")
     */
    private $endTime;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="events")
     * @ORM\JoinTable(name="calendar_event_users")
     * @Groups({"read"})
     */
    private $participants;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"list", "read"})
     */
    private $allDay = false;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getType(): ?CalendarEventType
    {
        return $this->type;
    }

    public function setType(?CalendarEventType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTime $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }

    /**
     * @Groups({"list", "read"})
     */
    public function getStart(): DateTimeInterface
    {
        $dt = clone $this->date;
        if ($dt instanceof DateTime) {
            $dt->setTime($this->startTime->format('H'), $this->startTime->format('i'));
        }

        return $dt;
    }

    /**
     * @Groups({"list", "read"})
     */
    public function getEnd(): DateTimeInterface
    {
        $dt = clone $this->date;
        if ($dt instanceof DateTime) {
            $dt->setTime($this->endTime->format('H'), $this->endTime->format('i'));
        }

        return $dt;
    }

    /**
     * @Groups({"list", "read"})
     */
    public function getBackgroundColor(): string
    {
        return '';
    }

    /**
     * @Groups({"list", "read"})
     */
    public function getTextColor(): string
    {
        return '';
    }

    /**
     * @Groups({"list", "read"})
     */
    public function getUrl(): string
    {
        return 'javascript:void(0)';
    }

    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): self
    {
        $this->allDay = $allDay;

        return $this;
    }
}
