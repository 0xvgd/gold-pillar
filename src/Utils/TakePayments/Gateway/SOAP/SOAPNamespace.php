<?php

namespace App\Utils\TakePayments\Gateway\SOAP;

class SOAPNamespace
{
    /**
     * @var string
     */
    private $m_szNamespace;
    /**
     * @var string
     */
    private $m_szPrefix;

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->m_szNamespace;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->m_szPrefix;
    }

    /**
     * @param string $szPrefix
     * @param string $szNamespace
     */
    public function __construct($szPrefix, $szNamespace)
    {
        $this->m_szNamespace = $szNamespace;
        $this->m_szPrefix = $szPrefix;
    }
}
