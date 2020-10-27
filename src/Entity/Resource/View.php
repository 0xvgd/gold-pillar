<?php

namespace App\Entity\Resource;

use App\Entity\Calendar\Event;
use App\Entity\Person\Agent;
use App\Entity\Security\User;
use App\Entity\Timestampable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="resource_views")
 * @ORM\Entity(repositoryClass="App\Repository\Renting\ViewRepository")
 */
class View
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
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity=Event::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"list"})
     */
    private $event;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list", "read"})
     */
    private $customer;

    /**
     * @var resource
     *
     * @ORM\ManyToOne(targetEntity=Resource::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list", "read"})
     */
    private $resource;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity=Agent::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list", "read"})
     */
    private $agent;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"list", "read"})
     */
    private $moveInDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"list", "read"})
     */
    private $peopleCount;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getMoveInDate(): ?\DateTimeInterface
    {
        return $this->moveInDate;
    }

    public function setMoveInDate(?\DateTimeInterface $moveInDate): self
    {
        $this->moveInDate = $moveInDate;

        return $this;
    }

    public function getPeopleCount(): ?int
    {
        return $this->peopleCount;
    }

    public function setPeopleCount(?int $peopleCount): self
    {
        $this->peopleCount = $peopleCount;

        return $this;
    }
}
