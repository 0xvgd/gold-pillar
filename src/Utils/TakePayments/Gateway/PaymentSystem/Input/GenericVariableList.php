<?php

namespace App\Utils\TakePayments\Gateway\PaymentSystem\Input;

use Exception;

class GenericVariableList
{
    /**
     * @var GenericVariable[]
     */
    private $m_lgvGenericVariableList;

    /**
     * @param int|string $intOrStringValue
     *
     * @return GenericVariable
     *
     * @throws Exception
     */
    public function getAt($intOrStringValue)
    {
        $nCount = 0;
        $boFound = false;
        $gvGenericVariable = null;

        if (is_int($intOrStringValue)) {
            if ($intOrStringValue < 0 ||
                $intOrStringValue >= count($this->m_lgvGenericVariableList)
            ) {
                throw new Exception('Array index out of bounds');
            }

            return $this->m_lgvGenericVariableList[$intOrStringValue];
        } elseif (is_string($intOrStringValue)) {
            if (null == $intOrStringValue || '' == $intOrStringValue) {
                return null;
            }

            while (!$boFound &&
                $nCount < count($this->m_lgvGenericVariableList)
            ) {
                if (strtoupper(
                    $this->m_lgvGenericVariableList[$nCount]->getName()
                ) == strtoupper($intOrStringValue)
                ) {
                    $gvGenericVariable =
                        $this->m_lgvGenericVariableList[$nCount];
                    $boFound = true;
                }
                ++$nCount;
            }

            return $gvGenericVariable;
        } else {
            throw new Exception("Invalid parameter type:$intOrStringValue");
        }
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->m_lgvGenericVariableList);
    }

    /**
     * @param string $szName
     * @param string $szValue
     */
    public function add($szName, $szValue)
    {
        if (null != $szName && '' != $szName) {
            $genericVariable = new GenericVariable();
            $genericVariable->setName($szName);
            $genericVariable->setValue($szValue);
            array_push($this->m_lgvGenericVariableList, $genericVariable);
        }
    }

    //constructor
    public function __construct()
    {
        $this->m_lgvGenericVariableList = [];
    }
}
