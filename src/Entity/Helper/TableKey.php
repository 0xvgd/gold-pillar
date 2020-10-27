<?php

namespace App\Entity\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Helper\TableKeyRepository")
 */
class TableKey implements \JsonSerializable
{
    const TRANSACTION_TYPE = 3;
    const PROJECT_TRANSACTION_TYPE = 7;
    const ASSET_TRANSACTION_TYPE = 9;
    const BALANCE_TRANSACTION_TYPE = 10;
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=TableValue::class,
     * mappedBy="tableKey", cascade={"all"}, orphanRemoval=true)
     */
    private $tableValues;

    public function __construct()
    {
        $this->tableValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

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
     * @return Collection|TableValue[]
     */
    public function getTableValues(): Collection
    {
        return $this->tableValues;
    }

    public function addTableValue(TableValue $tableValue): self
    {
        if (!$this->tableValues->contains($tableValue)) {
            $this->tableValues[] = $tableValue;
            $tableValue->setTableKey($this);
        }

        return $this;
    }

    public function removeTableValue(TableValue $tableValue): self
    {
        if ($this->tableValues->contains($tableValue)) {
            $this->tableValues->removeElement($tableValue);
            // set the owning side to null (unless already changed)
            if ($tableValue->getTableKey() === $this) {
                $tableValue->setTableKey(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getDescription();
    }

    public function unserialize($serialized)
    {
        return list(
            $this->id,
            $this->description
        ) = unserialize($serialized);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
        ];
    }
}
