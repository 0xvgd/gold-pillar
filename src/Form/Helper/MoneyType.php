<?php

namespace App\Form\Helper;

use App\Entity\Money;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [];

        if (isset($options['constraints'])) {
            $constraints = $options['constraints'];
        }

        $builder
            ->add('currency', CurrencyType::class, [
                'preferred_choices' => ['GBP', 'USD'],
                'choice_label' => function (string $code) {
                    return $code;
                },
            ])
            ->add('amount', NumberType::class, [
                'scale' => 2,
                'grouping' => false,
                'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_HALF_UP,
                'compound' => false,
                'constraints' => $constraints,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Money::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_money';
    }
}
