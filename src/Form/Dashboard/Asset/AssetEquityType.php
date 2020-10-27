<?php

namespace App\Form\Dashboard\Asset;

use App\Entity\Asset\AssetEquity;
use App\Form\Helper\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetEquityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', MoneyType::class, [
            ])
            ->add('monthRef', IntegerType::class, [
                'label' => 'MonthRef',
                'attr' => [
                    'placeholder' => 'Month Ref',
                ],
            ])
            ->add('yearRef', IntegerType::class, [
                'label' => 'YearRef',
                'attr' => [
                    'placeholder' => 'Year Ref',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssetEquity::class,
        ]);
    }
}
