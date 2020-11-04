<?php

namespace App\Utils\TakePayments\Gateway\Common;

use Exception;

class XmlParser
{
    /**
     * @var XmlTagList
     */
    private $m_xtlXmlTagList;

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

        if (null != $this->m_xtlXmlTagList) {
            $boReturnValue = $this->m_xtlXmlTagList->getValue(
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
        if (null != $this->m_xtlXmlTagList) {
            $boReturnValue = $this->m_xtlXmlTagList->getValue(
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
        if (null != $this->m_xtlXmlTagList) {
            $boReturnValue = $this->m_xtlXmlTagList->getValue(
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

    /**
     * @param string $szTagName
     *
     * @return XMLTag
     */
    public function getTag($szTagName)
    {
        $xmlReturnTag = null;

        if (0 == $this->m_xtlXmlTagList->getCount()) {
            return null;
        }

        $xmlReturnTag = $this->m_xtlXmlTagList->getXmlTag($szTagName);

        return $xmlReturnTag;
    }

    /**
     * @param string $szXmlString
     *
     * @return bool
     */
    public function parseBuffer($szXmlString)
    {
        $boReturnValue = false;

        try {
            $sxiSimpleXMLIterator = new \SimpleXMLIterator($szXmlString);

            $xtlXmlTagList = XmlParser::toXmlTagList($sxiSimpleXMLIterator);
            $this->m_xtlXmlTagList = new XmlTagList(
                $xtlXmlTagList->toListOfXmlTagObjects()
            );

            $boReturnValue = true;
        } catch (Exception $e) {
        }

        return $boReturnValue;
    }

    /**
     * @param SimpleXMLIterator $sxiSimpleXMLIterator
     *
     * @return XmlTag2List
     *
     * @throws Exception
     */
    private static function toXmlTagList(
        \SimpleXMLIterator $sxiSimpleXMLIterator
    ) {
        $xtlXmlTagList = new XmlTag2List();

        for ($sxiSimpleXMLIterator->rewind(); $sxiSimpleXMLIterator->valid(); $sxiSimpleXMLIterator->next()
        ) {
            $xtXmlTag = new XmlTag2();
            $xtlXmlTagList->add($xtXmlTag);
            $xtXmlTag->setName($sxiSimpleXMLIterator->key());

            if ('' != strval($sxiSimpleXMLIterator->current())) {
                $xtXmlTag->setContent(strval($sxiSimpleXMLIterator->current()));
            }

            // get the attributes
            foreach ($sxiSimpleXMLIterator->current()->attributes()
 as $szName => $szValue
            ) {
                $xaXmlAttribute = new XmlAttribute2();
                $xaXmlAttribute->setName($szName);
                $xaXmlAttribute->setValue(strval($szValue));
                $xtXmlTag->getXmlAttributes()->add($xaXmlAttribute);
            }

            // parse the child tags
            if ($sxiSimpleXMLIterator->hasChildren()) {
                $xtlChildXmlTagList = XmlParser::toXmlTagList(
                    $sxiSimpleXMLIterator->current()
                );

                for ($nCount = 0; $nCount < $xtlChildXmlTagList->getCount(); ++$nCount
                ) {
                    $xtXmlTag
                        ->getChildTags()
                        ->add($xtlChildXmlTagList->getAt($nCount));
                }
            }
        }

        return $xtlXmlTagList;
    }
}
