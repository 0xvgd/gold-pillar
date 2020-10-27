<?php

namespace App\Entity\Finance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="finance_payment_expenses")
 */
class ExpensePayment extends Payment
{
}
