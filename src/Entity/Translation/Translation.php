<?php

namespace App\Entity\Translation;

use App\Repository\Translation\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TranslationRepository::class)
 */
class Translation
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"read", "list"})
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    private $source;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $target;

    /**
     * @ORM\ManyToOne(targetEntity=Locale::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity=Translation::class)
     */
    private $base;

    public function __construct()
    {
        if ($this->getBase()) {
            $this->setSource($this->getBase()->getSource());
        }
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        if ($this->getBase()) {
            return $this->getBase()->getSource();
        }

        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    public function setLocale(?Locale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getBase(): ?self
    {
        return $this->base;
    }

    public function setBase(?self $base): self
    {
        $this->base = $base;

        return $this;
    }

    public function __toString()
    {
        return $this->getTarget() ?? '';
    }
}
