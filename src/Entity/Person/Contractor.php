<?php

namespace App\Entity\Person;

use App\Entity\Resource\Asset;
use App\Entity\Resource\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="person_contractors")
 * @ORM\Entity()
 */
class Contractor extends Person
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "list"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="contractor")
     * @Groups({"read"})
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity=Asset::class, mappedBy="contractor")
     * @Groups({"read"})
     */
    private $assets;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->assets = new ArrayCollection();
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
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setContractor($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getContractor() === $this) {
                $project->setContractor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Asset[]
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Asset $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->projects[] = $asset;
            $asset->setContractor($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->contains($asset)) {
            $this->assets->removeElement($asset);
            // set the owning side to null (unless already changed)
            if ($asset->getContractor() === $this) {
                $asset->setContractor(null);
            }
        }

        return $this;
    }
}
