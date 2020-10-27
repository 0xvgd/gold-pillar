<?php

namespace App\Entity\Finance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class WithdrawalTransaction extends Transaction
{
    public function isCredit(): bool
    {
        return false;
    }
}
