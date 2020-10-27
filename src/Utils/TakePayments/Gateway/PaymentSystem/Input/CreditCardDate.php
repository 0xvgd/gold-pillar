<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use App\Utils\TakePayments\Gateway\Common\NullableInt;

abstract class CreditCardDate
{
    /**
     * @var NullableInt
     */
    private $m_nMonth;
    /**
     * @var NullableInt
     */
    private $m_nYear;

    /**
     * @return NullableInt
     */
    public function getMonth()
    {
        return $this->m_nMonth;
    }

    /**
     * @return NullableInt
     */
    public function getYear()
    {
        return $this->m_nYear;
    }

    //constructor
    public function __construct()
    {
        $this->m_nMonth = new NullableInt();
        $this->m_nYear = new NullableInt();
    }
}
