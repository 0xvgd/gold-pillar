<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XmlTag
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
     * @var XmlTagList
     */
    private $m_xtlChildTags;
    /**
     * @var XmlAttributeList
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
     * @return string
     */
    public function getContent()
    {
        return $this->m_szContent;
    }

    /**
     * @return XmlAttributeList
     */
    public function getXmlAttributes()
    {
        return $this->m_xalXmlAttributes;
    }

    /**
     * @return XmlTagList
     */
    public function getChildTags()
    {
        return $this->m_xtlChildTags;
    }

    /**
     * @param string $szXMLVariable
     * @param string $szValue
     *
     * @return bool
     */
    public function getStringValue($szXMLVariable, &$szValue)
    {
        $boReturnValue = false;

        $szValue = '';

        if (null != $this->m_xtlChildTags) {
            $boReturnValue = $this->m_xtlChildTags->getValue(
                $szXMLVariable,
                $szValue
            );
        }

        return $boReturnValue;
    }

    /**
     * @param string $szXMLVariable
     * @param int    $nValue
     *
     * @return bool
     */
    public function getIntegerValue($szXMLVariable, &$nValue)
    {
        $boReturnValue = false;
        $szValue = '';

        $nValue = false;
        if (null != $this->m_xtlChildTags) {
            $boReturnValue = $this->m_xtlChildTags->getValue(
                $szXMLVariable,
                $szValue
            );

            if ($boReturnValue) {
                if (!is_numeric($szValue)) {
                    $boReturnValue = false;
                } else {
                    try {
                        $nValue = intval($szValue);
                    } catch (Exception $e) {
                        $boReturnValue = false;
                    }
                }
            }
        }

        return $boReturnValue;
    }

    /**
     * @param string $szXMLVariable
     * @param bool   $boValue
     *
     * @return bool
     */
    public function getBooleanValue($szXMLVariable, &$boValue)
    {
        $boReturnValue = false;
        $szValue = '';

        $boValue = false;
        if (null != $this->m_xtlChildTags) {
            $boReturnValue = $this->m_xtlChildTags->getValue(
                $szXMLVariable,
                $szValue
            );

            if ($boReturnValue) {
                if ('0' != $szValue &&
                    '1' != $szValue &&
                    'FALSE' != strtoupper($szValue) &&
                    'TRUE' != strtoupper($szValue)
                ) {
                    $boReturnValue = false;
                } else {
                    if ('TRUE' == strtoupper($szValue) || '1' == $szValue) {
                        $boValue = true;
                    }
                }
            }
        }

        return $boReturnValue;
    }

    //constructor

    /**
     * @param string          $szName
     * @param string          $szContent
     * @param XMLTag[]        $lxtChildTags
     * @param XMLAttribute2[] $lxaXmlAttributes
     */
    public function __construct(
        $szName,
        $szContent,
        $lxtChildTags,
        $lxaXmlAttributes
    ) {
        $this->m_szName = $szName;
        $this->m_szContent = $szContent;
        $this->m_xalXmlAttributes = new XmlAttributeList($lxaXmlAttributes);
        $this->m_xtlChildTags = new XmlTagList($lxtChildTags);
    }
}
