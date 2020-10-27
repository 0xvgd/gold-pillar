<?php

namespace App\Entity\Finance;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="finance_payment_commissions")
 */
class CommissionPayment extends Payment
{
    /**
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity=Commission::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"read", "list"})
     */
    private $commission;

    public function getCommission(): ?Commission
    {
        return $this->commission;
    }

    public function setCommission(?Commission $commission): self
    {
        $this->commission = $commission;

        return $this;
    }
}
