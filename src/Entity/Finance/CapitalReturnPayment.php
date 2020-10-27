<?php

namespace App\Entity\Finance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="finance_payment_capital_returns")
 */
class CapitalReturnPayment extends Payment
{
    /**
     * @var CapitalReturn
     *
     * @ORM\ManyToOne(targetEntity=CapitalReturn::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $capitalReturn;

    public function getCapitalReturn(): ?CapitalReturn
    {
        return $this->capitalReturn;
    }

    public function setCapitalReturn(?CapitalReturn $capitalReturn): self
    {
        $this->capitalReturn = $capitalReturn;

        return $this;
    }
}
