<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

class GenericVariable
{
    /**
     * @var string
     */
    private $m_szName;
    /**
     * @var string
     */
    private $m_szValue;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->m_szName;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->m_szName = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->m_szValue;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->m_szValue = $value;
    }

    //constructor
    public function __construct()
    {
        $this->m_szName = '';
        $this->m_szValue = '';
    }
}
