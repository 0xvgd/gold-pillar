<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XmlTag2List
{
    /**
     * @var XMLTag2[]
     */
    private $m_lxtXmlTagList;

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lxtXmlTagList);
    }

    /**
     * @param int $nIndex
     *
     * @return XMLTag2
     *
     * @throws Exception
     */
    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_lxtXmlTagList)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lxtXmlTagList[$nIndex];
    }

    public function add(XmlTag2 $xtXmlTag)
    {
        if (null != $xtXmlTag) {
            $this->m_lxtXmlTagList[] = $xtXmlTag;
        }
    }

    //constructor
    public function __construct()
    {
        $this->m_lxtXmlTagList = [];
    }

    /**
     * @return XMLTag[]
     */
    public function toListOfXmlTagObjects()
    {
        $nCount = 0;
        $lxtXmlTagList = [];

        for ($nCount = 0; $nCount < count($this->m_lxtXmlTagList); ++$nCount) {
            $lxtXmlTagList[] = $this->m_lxtXmlTagList[$nCount]->toXmlTag();
        }

        return $lxtXmlTagList;
    }
}
