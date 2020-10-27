<?php

namespace App\Utils\TakePayments\Gateway\SOAP;

use Exception;

class SOAPParamAttributeList
{
    /**
     * @var SOAPParamAttribute[]
     */
    private $m_lspaSOAPParamAttributeAttributeList;

    /**
     * @param int $nIndex
     *
     * @return SOAPParamAttribute
     *
     * @throws Exception
     */
    public function getAt($nIndex)
    {
        if ($nIndex < 0 ||
            $nIndex >= count($this->m_lspaSOAPParamAttributeAttributeList)
        ) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lspaSOAPParamAttributeAttributeList[$nIndex];
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lspaSOAPParamAttributeAttributeList);
    }

    public function add(SOAPParamAttribute $spaSOAPParamAttributeAttribute)
    {
        $this->m_lspaSOAPParamAttributeAttributeList[] = $spaSOAPParamAttributeAttribute;
    }

    //constructor
    public function __construct()
    {
        $this->m_lspaSOAPParamAttributeAttributeList = [];
    }
}
