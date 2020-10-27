<?php

namespace App\Utils\TakePayments\Helpers;

use Exception;

/* ############################################## */
/* List Item Class control */
/* ############################################## */
/**
 * [ListItemList List item control class for generating list items for adding to HTML docs ].
 */
class ListItemList
{
    private $m_lilListItemList;

    public function getCount()
    {
        return count($this->m_lilListItemList);
    }

    public function getAt($nIndex)
    {
        if ($nIndex < 0 || $nIndex >= count($this->m_lilListItemList)) {
            throw new Exception('Array index out of bounds');
        }

        return $this->m_lilListItemList[$nIndex];
    }

    public function add($Name, $Value, $boIsSelected)
    {
        $liListItem = new ListItem($Name, $Value, $boIsSelected);
        $this->m_lilListItemList[] = $liListItem;
    }

    /**
     * [toString Convert value to string].
     *
     * @method toString
     *
     * @return [String] [list item to string]
     */
    public function toString()
    {
        $ReturnString = '';
        for ($nCount = 0; $nCount < count($this->m_lilListItemList); ++$nCount
        ) {
            $liListItem = $this->m_lilListItemList[$nCount];
            $ReturnString = $ReturnString.'<option';
            if (null != $liListItem->getValue() &&
                '' != $liListItem->getValue()
            ) {
                $ReturnString =
                    $ReturnString.
                    ' value="'.
                    $liListItem->getValue().
                    '"';
            }
            if (true == $liListItem->getIsSelected()) {
                $ReturnString = $ReturnString.' selected="selected"';
            }
            $ReturnString =
                $ReturnString.'>'.$liListItem->getName()."</option>\n";
        }

        return $ReturnString;
    }

    //constructor
    public function __construct()
    {
        $this->m_lilListItemList = [];
    }
}
