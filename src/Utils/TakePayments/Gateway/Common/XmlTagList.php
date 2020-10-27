<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XmlTagList
{
    /**
     * @var XMLTag[]
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
     * @return XmlTag
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

    /**
     * @param string $szName
     *
     * @return XmlTag
     *
     * @throws Exception
     */
    public function getXmlTag($szName)
    {
        $lszHierarchicalNames = null;
        $nCount = 0;
        $boAbort = false;
        $boFound = false;
        $boLastNode = false;
        $szString = null;
        $szTagNameToFind = null;
        $nCurrentIndex = 0;
        $xtReturnTag = null;
        $xtCurrentTag = null;
        $nTagCount = 0;
        $xtlCurrentTagList = null;
        $nCount2 = 0;

        if (0 == count($this->m_lxtXmlTagList)) {
            return null;
        }

        $lszHierarchicalNames = new StringList();
        $lszHierarchicalNames = SharedFunctions::getStringListFromCharSeparatedString(
            $szName,
            '.'
        );

        $xtlCurrentTagList = $this;

        // loop over the hierarchical list
        for ($nCount = 0; $nCount < $lszHierarchicalNames->getCount() && !$boAbort; ++$nCount
        ) {
            if ($nCount == $lszHierarchicalNames->getCount() - 1) {
                $boLastNode = true;
            }

            $szString = $lszHierarchicalNames->getAt($nCount);

            // look to see if this tag name has the special "[]" array chars
            $szTagNameToFind = SharedFunctions::getArrayNameAndIndex(
                $szString,
                $nCurrentIndex
            );

            $boFound = false;
            $nCount2 = 0;

            for ($nTagCount = 0; $nTagCount < $xtlCurrentTagList->getCount() && !$boFound; ++$nTagCount
            ) {
                $xtCurrentTag = $xtlCurrentTagList->getAt($nTagCount);

                // if this is the last node then check the attributes of the tag first

                if ($xtCurrentTag->getName() == $szTagNameToFind) {
                    if ($nCount2 == $nCurrentIndex) {
                        $boFound = true;
                    } else {
                        ++$nCount2;
                    }
                }

                if ($boFound) {
                    if (!$boLastNode) {
                        $xtlCurrentTagList = $xtCurrentTag->getChildTags();
                    } else {
                        // don't continue the search
                        $xtReturnTag = $xtCurrentTag;
                    }
                }
            }

            if (!$boFound) {
                $boAbort = true;
            }
        }

        return $xtReturnTag;
    }

    /**
     * @param string $szXMLVariable
     * @param string $szValue       Passed by reference
     *
     * @return bool
     *
     * @throws Exception
     */
    public function getValue($szXMLVariable, &$szValue)
    {
        $boReturnValue = false;
        $lszHierarchicalNames = null;
        $szXMLTagName = null;
        $szLastXMLTagName = null;
        $nCount = 0;
        $xtCurrentTag = null;
        $xaXmlAttribute = null;

        $lszHierarchicalNames = new StringList();
        $szValue = null;
        $lszHierarchicalNames = SharedFunctions::getStringListFromCharSeparatedString(
            $szXMLVariable,
            '.'
        );

        if (1 == $lszHierarchicalNames->getCount()) {
            $szXMLTagName = $lszHierarchicalNames->getAt(0);

            $xtCurrentTag = $this->getXmlTag($szXMLTagName);

            if (null != $xtCurrentTag) {
                $xaXmlAttribute = $xtCurrentTag
                    ->getXmlAttributes()
                    ->getXmlAttribute($szXMLTagName);

                if (null != $xaXmlAttribute) {
                    $szValue = SharedFunctions::replaceEntitiesInStringWithChars(
                        $xaXmlAttribute->getValue()
                    );
                    $boReturnValue = true;
                } else {
                    $szValue = SharedFunctions::replaceEntitiesInStringWithChars(
                        $xtCurrentTag->getContent()
                    );
                    $boReturnValue = true;
                }
            }
        } else {
            if ($lszHierarchicalNames->getCount() > 1) {
                $szXMLTagName = $lszHierarchicalNames->getAt(0);
                $szLastXMLTagName = $lszHierarchicalNames->getAt(
                    $lszHierarchicalNames->getCount() - 1
                );

                // need to remove the last variable from the passed name
                for ($nCount = 1; $nCount < $lszHierarchicalNames->getCount() - 1; ++$nCount
                ) {
                    $szXMLTagName .=
                        '.'.$lszHierarchicalNames->getAt($nCount);
                }

                $xtCurrentTag = $this->getXmlTag($szXMLTagName);

                // first check the attributes of this tag
                if (null != $xtCurrentTag) {
                    $xaXmlAttribute = $xtCurrentTag
                        ->getXmlAttributes()
                        ->getXmlAttribute($szLastXMLTagName);

                    if (null != $xaXmlAttribute) {
                        $szValue = SharedFunctions::replaceEntitiesInStringWithChars(
                            $xaXmlAttribute->getValue()
                        );
                        $boReturnValue = true;
                    } else {
                        // check to see if it's actually a tag
                        if (null != $xtCurrentTag->getChildTags()) {
                            $xtCurrentTag = $xtCurrentTag
                                ->getChildTags()
                                ->getXmlTag($szLastXMLTagName);

                            if (null != $xtCurrentTag) {
                                $szValue = SharedFunctions::replaceEntitiesInStringWithChars(
                                    $xtCurrentTag->getContent()
                                );
                                $boReturnValue = true;
                            }
                        }
                    }
                }
            }
        }

        return $boReturnValue;
    }

    //constructor

    /**
     * @param XMLTag[] $lxtXmlTagList
     */
    public function __construct($lxtXmlTagList)
    {
        $nCount = 0;

        $this->m_lxtXmlTagList = [];

        if (null != $lxtXmlTagList) {
            try {
                for ($nCount = 0; $nCount < count($lxtXmlTagList); ++$nCount) {
                    $this->m_lxtXmlTagList[] = $lxtXmlTagList[$nCount];
                }
            } catch (Exception $e) {
            }
        }
    }

    /**
     * @return XMLTag[]
     */
    public function getListOfXmlTagObjects()
    {
        $nCount = 0;
        $lxtXmlTagList = [];

        for ($nCount = 0; $nCount < count($this->m_lxtXmlTagList); ++$nCount) {
            $lxtXmlTagList[] = $this->m_lxtXmlTagList[$nCount];
        }

        return $lxtXmlTagList;
    }
}
