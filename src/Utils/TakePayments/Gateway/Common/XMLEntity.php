<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XMLEntity
{
    /**
     * @var int
     */
    private $m_bCharCode;
    /**
     * @var string
     */
    private $m_szReplacement;

    /**
     * @return int
     */
    public function getCharCode()
    {
        return $this->m_bCharCode;
    }

    /**
     * @return string
     */
    public function getReplacement()
    {
        return $this->m_szReplacement;
    }

    //constructor

    /**
     * @param int    $bCharCode     Hexadecimal character code
     * @param string $szReplacement
     */
    public function __construct($bCharCode, $szReplacement)
    {
        $this->m_bCharCode = $bCharCode;
        $this->m_szReplacement = $szReplacement;
    }
}
