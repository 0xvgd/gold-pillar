<?php

namespace App\Utils\TakePayments\Helpers;

/**
 * [ListItem individual list items for adding to list item list].
 */
class ListItem
{
    private $m_Name;
    private $m_Value;
    private $m_boIsSelected;

    //public properties
    public function getName()
    {
        return $this->m_Name;
    }

    public function getValue()
    {
        return $this->m_Value;
    }

    public function getIsSelected()
    {
        return $this->m_boIsSelected;
    }

    //constructor
    public function __construct($Name, $Value, $boIsSelected)
    {
        $this->m_Name = $Name;
        $this->m_Value = $Value;
        $this->m_boIsSelected = $boIsSelected;
    }
}
