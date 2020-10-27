<?php

namespace App\Utils\TakePayments\Gateway\SOAP;

use Exception;

class SOAPParamList
{
    /**
     * @var SOAPParameter[]
     */
    private $m_lspSOAPParamList;

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lspSOAPParamList);
    }

    /**
     * @param int $nIndex
     *
     * @return SOAPParamter
     *
     * @throws Exception
     */
    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex > count($this->m_lspSOAPParamList)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lspSOAPParamList[$nIndex];
    }

    /**
     * @param string $szTagNameToFind
     * @param int    $nIndex
     *
     * @return SOAPParamter|null
     *
     * @throws Exception
     */
    public function isSOAPParamInList($szTagNameToFind, $nIndex)
    {
        $spReturnParam = null;
        $boFound = false;
        $nFound = 0;
        $nCount = 0;
        $spCurrentParam = null;

        while (!$boFound && $nCount < $this->getCount()) {
            $spCurrentParam = $this->getAt($nCount);

            if ($spCurrentParam->getName() == $szTagNameToFind) {
                if ($nFound == $nIndex) {
                    $boFound = true;
                    $spReturnParam = $spCurrentParam;
                } else {
                    ++$nFound;
                }
            }

            ++$nCount;
        }

        return $spReturnParam;
    }

    public function add(SOAPParameter $spSOAPParam)
    {
        $this->m_lspSOAPParamList[] = $spSOAPParam;
    }

    //constructor
    public function __construct()
    {
        $this->m_lspSOAPParamList = [];
    }
}
