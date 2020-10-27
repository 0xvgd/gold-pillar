<?php

namespace App\Form;

use App\Enum\PropertyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * SearchProductType.
 */
class SearchProductType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $minValue = 0;
        $maxValue = 1000000;

        $builder
            ->add('propertyType', ChoiceType::class, [
                'label' => 'Select',
                'required' => false,
                'choices' => PropertyType::values(),
                'choice_label' => function (PropertyType $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?PropertyType $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('minValue', MoneyType::class, [
                'label' => 'Min Price',
                'currency' => false,
                'required' => false,
                'divisor' => 100,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => $minValue,
                    ]),
                ],
            ])
            ->add('maxValue', MoneyType::class, [
                'label' => 'Max Price',
                'currency' => false,
                'required' => false,
                'divisor' => 100,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => $maxValue,
                    ]),
                ],
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'required' => false,
            ])
            ->add('minBedroom', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'No min' => null,
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                ],
            ])
            ->add('maxBedroom', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'No max' => null,
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
