<?php

namespace App\Entity\Finance;

use App\Entity\Resource\Resource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Finance\RecurringIncomeRepository")
 */
class RecurringIncome extends Recurring
{
    /**
     * @var resource
     *
     * @ORM\ManyToOne(targetEntity=Resource::class, inversedBy="recurringIncomes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $resource;

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }
}
