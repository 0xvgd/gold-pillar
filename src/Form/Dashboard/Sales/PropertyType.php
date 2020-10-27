<?php

namespace App\Form\Dashboard\Sales;

use App\Entity\Resource\Property as Entity;
use App\Enum\PropertyType as EnumPropertyType;
use App\Form\Dashboard\ResourceType;
use App\Form\Helper\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends ResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('price', MoneyType::class, [
                'label' => 'Price',
            ])
            ->add('propertyType', ChoiceType::class, [
                'label' => 'Property type',
                'choices' => EnumPropertyType::values(),
                'choice_label' => function (EnumPropertyType $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?EnumPropertyType $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('deadline', DateType::class, [
                'required' => false,
                'label' => 'Deadline',
                'widget' => 'single_text',
            ])
            ->add('commissionRate', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('bedrooms', NumberType::class, [])
            ->add('bathrooms', NumberType::class, [])
            ->add('squareFoot', NumberType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Entity::class,
            ]);
    }
}
