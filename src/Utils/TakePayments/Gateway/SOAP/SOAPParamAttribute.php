<?php

namespace App\Utils\TakePayments\Gateway\SOAP;

use Exception;

class SOAPParamAttribute
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
     * @return string
     */
    public function getValue()
    {
        return $this->m_szValue;
    }

    //constructor

    /**
     * @param string $szName
     * @param string $szValue
     *
     * @throws Exception
     */
    public function __construct($szName, $szValue)
    {
        if (!is_string($szName) || !is_string($szValue)) {
            throw new Exception('Invalid parameter type');
        }

        $this->m_szName = $szName;
        $this->m_szValue = $szValue;
    }
}
