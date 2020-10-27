<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XmlAttributeList
{
    /**
     * @var XmlAttribute2[]
     */
    private $m_lxaXmlAttributeList;

    /**
     * @param string $szName
     *
     * @return XmlAttribute2
     */
    public function getXmlAttribute($szName)
    {
        $boFound = false;
        $nCount = 0;
        $xaXmlAttribute = null;
        $xaReturnXmlAttribute = null;

        while (!$boFound && $nCount < count($this->m_lxaXmlAttributeList)) {
            $xaXmlAttribute = $this->m_lxaXmlAttributeList[$nCount];

            if ($szName == $xaXmlAttribute->getName()) {
                $xaReturnXmlAttribute = $xaXmlAttribute;
                $boFound = true;
            }

            ++$nCount;
        }

        return $xaReturnXmlAttribute;
    }

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

    //constructor

    /**
     * @param XmlAttribute2[] $lxaXmlAttributeList
     */
    public function __construct($lxaXmlAttributeList)
    {
        $nCount = 0;

        $this->m_lxaXmlAttributeList = [];

        if (null != $lxaXmlAttributeList) {
            try {
                for ($nCount = 0; $nCount < count($lxaXmlAttributeList); ++$nCount
                ) {
                    $this->m_lxaXmlAttributeList[] =
                        $lxaXmlAttributeList[$nCount];
                }
            } catch (Exception $e) {
            }
        }
    }

    /**
     * @return XmlAttribute2[]
     */
    public function getListOfXmlAttributeObjects()
    {
        $nCount = 0;
        $lxaXmlAttributeList = [];

        for ($nCount = 0; $nCount < count($this->m_lxaXmlAttributeList); ++$nCount
        ) {
            $lxaXmlAttributeList[] = $this->m_lxaXmlAttributeList[$nCount];
        }

        return $lxaXmlAttributeList;
    }
}
