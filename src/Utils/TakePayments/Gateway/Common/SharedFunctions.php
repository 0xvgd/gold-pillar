<?php

namespace App\Utils\TakePayments\Gateway\Common;

class SharedFunctions
{
    public static function getNamedTagInTagList($szName, $xtlTagList)
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

        if (is_null($xtlTagList)) {
            return null;
        }

        if (0 == count($xtlTagList)) {
            return null;
        }

        $lszHierarchicalNames = new StringList();

        $lszHierarchicalNames = SharedFunctions::getStringListFromCharSeparatedString(
            $szName,
            '.'
        );

        $xtlCurrentTagList = $xtlTagList;

        // loop over the hierarchical list
        for ($nCount = 0; $nCount < $lszHierarchicalNames->getCount() && !$boAbort; ++$nCount
        ) {
            if ($nCount == $lszHierarchicalNames->getCount() - 1) {
                $boLastNode = true;
            }

            $szString = (string) $lszHierarchicalNames[$nCount];
            $nIndex = null;
            // look to see if this tag name has the special "[]" array chars
            $szTagNameToFind = SharedFunctions::getArrayNameAndIndex(
                $szString,
                $nCurrentIndex
            );
            $nCurrentIndex = $nIndex;

            $boFound = false;
            $nCount2 = 0;

            for ($nTagCount = 0; $nTagCount < $xtlCurrentTagList->getCount() && !$boFound; ++$nTagCount
            ) {
                $xtCurrentTag = $xtlCurrentTagList->getXmlTagForIndex(
                    $nTagCount
                );

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

    public static function getStringListFromCharSeparatedString(
        $szString,
        $cDelimiter
    ) {
        $nCount = 0;
        $nLastCount = -1;
        $szSubString = null;
        $nStringLength = null;
        $lszStringList = null;

        if (null == $szString ||
            '' == $szString ||
            '' == (string) $cDelimiter
        ) {
            return null;
        }

        $lszStringList = new StringList();

        $nStringLength = strlen($szString);

        for ($nCount = 0; $nCount < $nStringLength; ++$nCount) {
            if ($szString[$nCount] == $cDelimiter) {
                $szSubString = substr(
                    $szString,
                    $nLastCount + 1,
                    $nCount - $nLastCount - 1
                );
                $nLastCount = $nCount;
                $lszStringList->add($szSubString);

                if ($nCount == $nStringLength) {
                    $lszStringList->add('');
                }
            } else {
                if ($nCount == $nStringLength - 1) {
                    $szSubString = substr(
                        $szString,
                        $nLastCount + 1,
                        $nCount - $nLastCount
                    );
                    $lszStringList->add($szSubString);
                }
            }
        }

        return $lszStringList;
    }

    public static function getValue($szXMLVariable, $xtlTagList, &$szValue)
    {
        $boReturnValue = false;
        $lszHierarchicalNames = null;
        $szXMLTagName = null;
        $szLastXMLTagName = null;
        $nCount = 0;
        $xtCurrentTag = null;
        $xaXmlAttribute = null;
        $lXmlTagAttributeList = null;

        if (null == $xtlTagList) {
            $szValue = null;

            return false;
        }

        $lszHierarchicalNames = new StringList();
        $szValue = null;
        $lszHierarchicalNames = SharedFunctions::getStringListFromCharSeparatedString(
            $szXMLVariable,
            '.'
        );

        if (1 == count($lszHierarchicalNames)) {
            $szXMLTagName = $lszHierarchicalNames->getAt(0);

            $xtCurrentTag = SharedFunctions::GetNamedTagInTagList(
                $szXMLTagName,
                $xtlTagList
            );

            if (null != $xtCurrentTag) {
                $lXmlTagAttributeList = $xtCurrentTag->getAttributes();
                $xaXmlAttribute = $lXmlTagAttributeList->getAt($szXMLTagName);

                if (null != $xaXmlAttribute) {
                    $szValue = $xaXmlAttribute->getValue();
                    $boReturnValue = true;
                } else {
                    $szValue = $xtCurrentTag->getContent();
                    $boReturnValue = true;
                }
            }
        } else {
            if (count($lszHierarchicalNames) > 1) {
                $szXMLTagName = $lszHierarchicalNames->getAt(0);
                $szLastXMLTagName = $lszHierarchicalNames->getAt(
                    $lszHierarchicalNames->getCount() - 1
                );

                // need to remove the last variable from the passed name
                for ($nCount = 1; $nCount < $lszHierarchicalNames->getCount() - 1; ++$nCount) {
                    $szXMLTagName .=
                        '.'.$lszHierarchicalNames->getAt($nCount);
                }

                $xtCurrentTag = SharedFunctions::getNamedTagInTagList(
                    $szXMLTagName,
                    $xtlTagList
                );

                // first check the attributes of this tag
                if (null != $xtCurrentTag) {
                    $lXmlTagAttributeList = $xtCurrentTag->getAttributes();
                    $xaXmlAttribute = $lXmlTagAttributeList->getXmlAttributeForAttributeName(
                        $szLastXMLTagName
                    );

                    if (null != $xaXmlAttribute) {
                        $szValue = $xaXmlAttribute->getValue();
                        $boReturnValue = true;
                    } else {
                        // check to see if it's actually a tag
                        $xtCurrentTag = SharedFunctions::getNamedTagInTagList(
                            $szLastXMLTagName,
                            $xtCurrentTag->getChildTags()
                        );

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

        return $boReturnValue;
    }

    public static function getArrayNameAndIndex($szName, &$nIndex)
    {
        $szReturnString = null;
        $nCount = 0;
        $szSubString = null;
        $boFound = false;
        $boAbort = false;
        $boAtLeastOneDigitFound = false;

        if ('' == $szName) {
            $nIndex = 0;

            return $szName;
        }

        $szReturnString = $szName;
        $nIndex = 0;

        if (']' == $szName[strlen($szName) - 1]) {
            $nCount = strlen($szName) - 2;

            while (!$boFound && !$boAbort && $nCount >= 0) {
                // if we've found the closing array brace
                if ('[' == $szName[$nCount]) {
                    $boFound = true;
                } else {
                    if (!is_numeric($szName[$nCount])) {
                        $boAbort = true;
                    } else {
                        $boAtLeastOneDigitFound = true;
                        --$nCount;
                    }
                }
            }

            // did we finish successfully?
            if ($boFound && $boAtLeastOneDigitFound) {
                $szSubString = substr(
                    $szName,
                    $nCount + 1,
                    strlen($szName) - $nCount - 2
                );
                $szReturnString = substr($szName, 0, $nCount);
                $nIndex = (int) $szSubString;
            }
        }

        return $szReturnString;
    }

    public static function stringToByteArray($str)
    {
        $encoded = null;

        $encoded = utf8_encode($str);

        return $encoded;
    }

    public static function byteArrayToString($aByte)
    {
        return utf8_decode($aByte);
    }

    public static function forwardPaddedNumberString(
        $nNumber,
        $nPaddingAmount,
        $cPaddingChar
    ) {
        $szReturnString = null;
        $sbString = null;
        $nCount = 0;

        $szReturnString = (string) $nNumber;

        if (strlen($szReturnString) < $nPaddingAmount && $nPaddingAmount > 0) {
            $sbString = '';

            for ($nCount = 0; $nCount < $nPaddingAmount - strlen($szReturnString); ++$nCount
            ) {
                $sbString .= $cPaddingChar;
            }

            $sbString .= $szReturnString;
            $szReturnString = (string) $sbString;
        }

        return $szReturnString;
    }

    public static function stripAllWhitespace($szString)
    {
        $sbReturnString = null;
        $nCount = 0;

        if (null == $szString) {
            return null;
        }

        $sbReturnString = '';

        for ($nCount = 0; $nCount < strlen($szString); ++$nCount) {
            if (' ' != $szString[$nCount] &&
                '\t' != $szString[$nCount] &&
                '\n' != $szString[$nCount] &&
                '\r' != $szString[$nCount]
            ) {
                $sbReturnString .= $szString[$nCount];
            }
        }

        return (string) $sbReturnString;
    }

    public static function getAmountCurrencyString($nAmount, $nExponent)
    {
        $szReturnString = '';
        $lfAmount = null;
        $nDivideAmount = null;

        $nDivideAmount = (int) pow(10, $nExponent);
        $lfAmount = (float) ($nAmount / $nDivideAmount);
        $szReturnString = (string) $lfAmount;

        return $szReturnString;
    }

    public static function isStringNullOrEmpty($szString)
    {
        $boReturnValue = false;

        if (null == $szString || '' == $szString) {
            $boReturnValue = true;
        }

        return $boReturnValue;
    }

    public static function replaceCharsInStringWithEntities($szString)
    {
        //give access to enum like associated array
        global $g_XMLEntities;

        $szReturnString = null;
        $nCount = null;
        $boFound = null;
        $nHTMLEntityCount = null;

        $szReturnString = htmlspecialchars($szString);

        return $szReturnString;
    }

    public static function replaceEntitiesInStringWithChars($szString)
    {
        $szReturnString = null;
        $nCount = null;
        $boFound = false;
        $boFoundAmpersand = false;
        $nHTMLEntityCount = null;
        $szAmpersandBuffer = '';
        $nAmpersandBufferCount = 0;

        $szReturnString = html_entity_decode($szString);

        return $szReturnString;
    }

    public static function boolToString($boValue)
    {
        if (true == $boValue) {
            return 'true';
        } elseif (false == $boValue) {
            return 'false';
        }
    }
}
