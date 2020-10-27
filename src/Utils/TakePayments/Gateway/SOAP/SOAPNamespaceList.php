<?php

namespace App\Utils\TakePayments\Gateway\SOAP;

use Exception;

class SOAPNamespaceList
{
    /**
     * @var SOAPNamespace[]
     */
    private $m_lsnSOAPNamespaceList;

    /**
     * @param int $nIndex
     *
     * @return SOAPNamespace
     *
     * @throws Exception
     */
    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_lsnSOAPNamespaceList)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lsnSOAPNamespaceList[$nIndex];
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lsnSOAPNamespaceList);
    }

    public function add(SOAPNamespace $snSOAPNamespace)
    {
        $this->m_lsnSOAPNamespaceList[] = $snSOAPNamespace;
    }

    //constructor
    public function __construct()
    {
        $this->m_lsnSOAPNamespaceList = [];
    }
}
