<?php

namespace App\Entity\Finance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="finance_payment_dividends")
 */
class DividendPayment extends Payment
{
    /**
     * @var Dividend
     *
     * @ORM\ManyToOne(targetEntity=Dividend::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $dividend;

    public function getDividend(): ?Dividend
    {
        return $this->dividend;
    }

    public function setDividend(?Dividend $dividend): self
    {
        $this->dividend = $dividend;

        return $this;
    }
}
