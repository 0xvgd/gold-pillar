<?php

namespace App\Form\Dashboard;

use App\Entity\AssetInvestmentClass;
use App\Form\Helper\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetInvestmentClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('minAmount', MoneyType::class, [
            ])
            ->add('maxAmount', MoneyType::class, [
                'required' => false,
            ])
            ->add('equity', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssetInvestmentClass::class,
        ]);
    }
}
