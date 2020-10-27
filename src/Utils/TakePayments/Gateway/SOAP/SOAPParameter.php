<?php

namespace App\Utils\TakePayments\Gateway\SOAP;

use App\Utils\TakePayments\Gateway\Common\SharedFunctions;

class SOAPParameter
{
    /**
     * @var string
     */
    private $m_szName;
    /**
     * @var string
     */
    private $m_szValue;
    //private $m_lspaSOAPParamAttributeList = array();
    /**
     * @var SOAPParamAttributeList
     */
    private $m_lspaSOAPParamAttributeList;
    /**
     * @var SOAPParamList
     */
    private $m_lspSOAPParamList;

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
     * @return SOAPParamAttributeList
     */
    public function getSOAPParamAttributeList()
    {
        return $this->m_lspaSOAPParamAttributeList;
    }

    /**
     * @return SOAPParamList
     */
    public function getSOAPParamList()
    {
        return $this->m_lspSOAPParamList;
    }

    //constructor

    /**
     * @param string                 $szName
     * @param string                 $szValue
     * @param SOAPParamAttributeList $lspaSOAPParamAttributeList
     *
     * @throws Exception
     */
    public function __construct(
        $szName,
        $szValue,
        SOAPParamAttributeList $lspaSOAPParamAttributeList = null
    ) {
        $nCount = 0;
        $spaSOAPParamAttribute = null;

        if (!is_string($szName) || !is_string($szValue)) {
            throw new \Exception("Invalid parameter type - not strings $szName and $szValue");
        }

        $this->m_szName = $szName;
        //$this->m_szValue = Common\SharedFunctions::replaceCharsInStringWithEntities($szValue);
        $this->setValue($szValue);

        $this->m_lspSOAPParamList = new SOAPParamList();
        $this->m_lspaSOAPParamAttributeList = new SOAPParamAttributeList();

        if (null != $lspaSOAPParamAttributeList) {
            for ($nCount = 0; $nCount < $lspaSOAPParamAttributeList->getCount(); ++$nCount
            ) {
                $spaSOAPParamAttribute = new SOAPParamAttribute(
                    $lspaSOAPParamAttributeList->getAt($nCount)->getName(),
                    $lspaSOAPParamAttributeList->getAt($nCount)->getValue()
                );

                $this->m_lspaSOAPParamAttributeList->add(
                    $spaSOAPParamAttribute
                );
            }
        }
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function toXMLString()
    {
        $sbReturnString = null;
        $nCount = null;
        $spParam = null;
        $spaAttribute = null;
        $sbString = null;

        $sbReturnString = '';
        $sbReturnString .= '<'.$this->getName();

        if (null != $this->m_lspaSOAPParamAttributeList) {
            for ($nCount = 0; $nCount < $this->m_lspaSOAPParamAttributeList->getCount(); ++$nCount
            ) {
                $spaAttribute = $this->m_lspaSOAPParamAttributeList->getAt(
                    $nCount
                );

                if (null != $spaAttribute) {
                    $sbString = '';
                    $sbString .=
                        ' '.
                        $spaAttribute->getName().
                        '="'.
                        SharedFunctions::replaceCharsInStringWithEntities(
                            $spaAttribute->getValue()
                        ).
                        '"';
                    $sbReturnString .= (string) $sbString;
                }
            }
        }

        if (0 == $this->m_lspSOAPParamList->getCount() &&
            '' == $this->getValue()
        ) {
            $sbReturnString .= ' />';
        } else {
            $sbReturnString .= '>';

            if ('' != $this->getValue()) {
                $sbReturnString .= SharedFunctions::replaceCharsInStringWithEntities(
                    $this->getValue()
                );
            }

            for ($nCount = 0; $nCount < $this->m_lspSOAPParamList->getCount(); ++$nCount
            ) {
                $spParam = $this->m_lspSOAPParamList->getAt($nCount);

                if (null != $spParam) {
                    $sbReturnString .= $spParam->toXMLString();
                }
            }

            $sbReturnString .= '</'.$this->getName().'>';
        }

        return (string) $sbReturnString;
    }
}
