<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XmlTag2
{
    /**
     * @var string
     */
    private $m_szName;
    /**
     * @var string
     */
    private $m_szContent;
    /**
     * @var XmlTag2List
     */
    private $m_xtlChildTags;
    /**
     * @var XmlAttribute2List
     */
    private $m_xalXmlAttributes;

    //public properties

    /**
     * @return string
     */
    public function getName()
    {
        return $this->m_szName;
    }

    /**
     * @param $szName
     */
    public function setName($szName)
    {
        $this->m_szName = $szName;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->m_szContent;
    }

    /**
     * @param string $szContent
     */
    public function setContent($szContent)
    {
        $this->m_szContent = $szContent;
    }

    /**
     * @return XmlAttribute2List
     */
    public function getXmlAttributes()
    {
        return $this->m_xalXmlAttributes;
    }

    /**
     * @return XmlTag2List
     */
    public function getChildTags()
    {
        return $this->m_xtlChildTags;
    }

    //constructor
    public function __construct()
    {
        $this->m_xalXmlAttributes = new XmlAttribute2List();
        $this->m_xtlChildTags = new XmlTag2List();
    }

    /**
     * @return XmlTag
     */
    public function toXmlTag()
    {
        $xtXmlTag = new XmlTag(
            $this->m_szName,
            $this->m_szContent,
            $this->m_xtlChildTags->toListOfXmlTagObjects(),
            $this->m_xalXmlAttributes->toListOfXmlAttributeObjects()
        );

        return $xtXmlTag;
    }
}
