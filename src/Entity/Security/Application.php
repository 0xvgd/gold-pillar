<?php

namespace App\Entity\Security;

use App\Entity\ApplicationSettings;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="security_application")
 */
class Application implements JsonSerializable
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", length=255)
     */
    private $hasUnits;

    /**
     * @ORM\OneToOne(
     *  targetEntity=ApplicationSettings::class,
     *  mappedBy="application",
     *  fetch="EXTRA_LAZY",
     *  cascade={"persist", "remove"}
     * )
     */
    private $applicationSettings;

    public function getApplicationSettings(): ?ApplicationSettings
    {
        return $this->applicationSettings;
    }

    public function setApplicationSettings(?ApplicationSettings $applicationSettings): self
    {
        $this->applicationSettings = $applicationSettings;

        // set (or unset) the owning side of the relation if necessary
        $newApplication = null === $applicationSettings ? null : $this;
        if ($newApplication !== $applicationSettings->getApplication()) {
            $applicationSettings->setApplication($newApplication);
        }

        return $this;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getHasUnits(): ?string
    {
        return $this->hasUnits;
    }

    public function setHasUnits(string $hasUnits): self
    {
        $this->hasUnits = $hasUnits;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        ];
    }
}
