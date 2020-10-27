<?php

namespace App\Form\Dashboard\Investor;

use App\Form\Frontend\OrderDetailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class NewInvestmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $min = $options['min'];
        $max = $options['max'];
        $disabled = $max <= 0;

        $builder
            // ->add('transactionType', ChoiceType::class, [
            //     'disabled' => $disabled,
            //     'expanded' => true,
            //     'choices' => [
            //         'CreditCard' => 'credit',
            //         'Deposit' => 'deposit',
            //     ],
            // ])
            ->add('amount', NumberType::class, [
                'disabled' => $disabled,
                'scale' => 2,
                'grouping' => false,
                'rounding_mode' => \NumberFormatter::ROUND_HALFUP,
                'compound' => false,
                'attr' => [
                    'placeholder' => 'Amount',
                    'max' => $max,
                    'min' => 1,
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => $max,
                    ]),
                ],
            ])
            ->add('orderDetail', OrderDetailType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'min', 'max',
            ]);
    }
}
