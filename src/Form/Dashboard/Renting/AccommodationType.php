<?php

namespace App\Form\Dashboard\Renting;

use App\Entity\Resource\Accommodation as Entity;
use App\Enum\PropertyType;
use App\Enum\RentingPlan;
use App\Enum\TermType;
use App\Form\Dashboard\ResourceType;
use App\Form\Helper\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccommodationType extends ResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('propertyType', ChoiceType::class, [
                'label' => 'Property type',
                'placeholder' => '',
                'choices' => PropertyType::values(),
                'choice_label' => function (PropertyType $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?PropertyType $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('plan', ChoiceType::class, [
                'label' => 'Renting plan',
                'placeholder' => '',
                'choices' => RentingPlan::values(),
                'choice_label' => function (RentingPlan $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?RentingPlan $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('rent', MoneyType::class, [
                'label' => 'Rent',
            ])
            ->add('deposit', MoneyType::class, [
                'label' => 'Deposit',
            ])
            ->add('terms', NumberType::class, [
                'label' => 'Terms',
            ])
            ->add('termType', ChoiceType::class, [
                'label' => 'Term type',
                'choices' => TermType::values(),
                'choice_label' => function (TermType $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function ($enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('letAvailableFor', NumberType::class, [
                'label' => 'Let available for',
            ])
            ->add('deadline', DateType::class, [
                'required' => false,
                'label' => 'Deadline',
                'widget' => 'single_text',
            ])
            ->add('availableAt', DateType::class, [
                'required' => false,
                'label' => 'Available from',
                'widget' => 'single_text',
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
