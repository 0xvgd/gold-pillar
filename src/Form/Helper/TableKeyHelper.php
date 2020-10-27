<?php

namespace App\Form\Helper;

use App\Entity\Helper\TableValue;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\NotNull;

class TableKeyHelper
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $tableKey
     * @param string $label
     * @param bool   $required
     * @param bool   $mapped
     * @param string $orderBy
     * @param string $placeholder
     * @param bool   $multiple
     * @param bool   $expanded
     *
     * @return array
     */
    public function formOptions(
        $tableKey,
        $label,
        $required = true,
        $mapped = true,
        $orderBy = 'description',
        $placeholder = '(Select)',
        $multiple = false,
        $expanded = false
    ) {
        $query = $this->em
                ->createQueryBuilder()
                ->select('tv')
                ->from(TableValue::class, 'tv')
                ->where('tv.tableKey = :key')
                ->orderBy("tv.$orderBy")
                ->setParameter('key', $tableKey)
                ->getQuery()
                ->useResultCache(true, 86400);

        return [
            'label' => $label,
            'class' => TableValue::class,
            'placeholder' => $placeholder,
            'choices' => $query->getResult(),
            'required' => $required,
            'mapped' => $mapped,
            'multiple' => $multiple,
            'expanded' => $expanded,
            'constraints' => $required ? [new NotNull()] : [],
        ];
    }
}
