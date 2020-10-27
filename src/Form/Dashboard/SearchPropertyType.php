<?php

namespace App\Form\Dashboard;

use App\Enum\PropertyStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SearchPropertyType.
 */
class SearchPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => PropertyStatus::values(),
                'choice_label' => function (PropertyStatus $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?PropertyStatus $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ]);
    }
}
