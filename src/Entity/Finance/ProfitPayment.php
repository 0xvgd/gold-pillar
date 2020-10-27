<?php

namespace App\Entity\Finance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="finance_payment_profits")
 */
class ProfitPayment extends Payment
{
    /**
     * @var Profit
     *
     * @ORM\ManyToOne(targetEntity=Profit::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $profit;

    public function getProfit(): ?Profit
    {
        return $this->profit;
    }

    public function setProfit(?Profit $profit): self
    {
        $this->profit = $profit;

        return $this;
    }
}
