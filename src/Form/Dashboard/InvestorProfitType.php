<?php

namespace App\Form\Dashboard;

use App\Entity\InvestorProfit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvestorProfitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minAmount', NumberType::class, [
                'scale' => 2,
                'grouping' => false,
                'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_HALF_UP,
                'compound' => false,
                'attr' => [
                    'placeholder' => 'Monthly Fee',
                ],
            ])
            ->add('maxAmount', NumberType::class, [
                'scale' => 2,
                'grouping' => false,
                'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_HALF_UP,
                'compound' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Monthly Fee',
                ],
            ])
            ->add('profit', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InvestorProfit::class,
        ]);
    }
}
