<?php

namespace App\Entity\Resource;

use App\Entity\Money;

interface InvestiblaInterface extends ResourceInterface
{
    public function getMinInvestmentValue(): float;

    public function getMaxInvestmentValue(): float;

    public function getTotalInvested(): Money;

    public function setTotalInvested(Money $total);
}
