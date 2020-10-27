<?php

namespace App\Entity\Translation;

use App\Repository\Translation\LocaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LocaleRepository::class)
 */
class Locale
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"list", "read"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"list", "read"})
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity=Translation::class, mappedBy="locale", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"source" = "ASC"})
     */
    private $translations;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"list", "read"})
     */
    private $defaultLocale;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDefaultLocale(): ?bool
    {
        return $this->defaultLocale;
    }

    public function setDefaultLocale(bool $defaultLocale): self
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * @return Collection|Translation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(Translation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setLocale($this);
        }

        return $this;
    }

    public function removeTranslation(Translation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }

        return $this;
    }
}
