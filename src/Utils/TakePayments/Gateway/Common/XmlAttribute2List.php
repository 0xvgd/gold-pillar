<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XmlAttribute2List
{
    /**
     * @var XmlAttribute2[]
     */
    private $m_lxaXmlAttributeList;

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lxaXmlAttributeList);
    }

    /**
     * @param int $nIndex
     *
     * @return XmlAttribute2
     *
     * @throws Exception
     */
    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_lxaXmlAttributeList)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lxaXmlAttributeList[$nIndex];
    }

    public function add(XmlAttribute2 $xaXmlAttribute)
    {
        if (null != $xaXmlAttribute) {
            $this->m_lxaXmlAttributeList[] = $xaXmlAttribute;
        }
    }

    //constructor
    public function __construct()
    {
        $this->m_lxaXmlAttributeList = [];
    }

    /**
     * @return XmlAttribute2[]
     */
    public function toListOfXmlAttributeObjects()
    {
        $nCount = 0;
        $lxaXmlAttributeList = [];

        for ($nCount = 0; $nCount < count($this->m_lxaXmlAttributeList); ++$nCount
        ) {
            $lxaXmlAttributeList[] = $this->m_lxaXmlAttributeList[
                $nCount
            ]->toXmlAttribute();
        }

        return $lxaXmlAttributeList;
    }
}
