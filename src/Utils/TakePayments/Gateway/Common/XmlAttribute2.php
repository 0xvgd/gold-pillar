<?php

namespace App\Utils\TakePayments\Gateway\Common;

class XmlAttribute2
{
    /**
     * @var string
     */
    private $m_szName;
    /**
     * @var string
     */
    private $m_szValue;

    //public properties

    /**
     * @return string
     */
    public function getName()
    {
        return $this->m_szName;
    }

    /**
     * @param string $szName
     */
    public function setName($szName)
    {
        $this->m_szName = $szName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->m_szValue;
    }

    /**
     * @param string $szValue
     */
    public function setValue($szValue)
    {
        $this->m_szValue = $szValue;
    }

    /**
     * @return XmlAttribute
     */
    public function toXmlAttribute()
    {
        $xaXmlAttribute = new XmlAttribute($this->m_szName, $this->m_szValue);

        return $xaXmlAttribute;
    }
}
