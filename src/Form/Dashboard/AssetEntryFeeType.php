<?php

namespace App\Form\Dashboard;

use App\Entity\AssetEntryFee;
use App\Form\Helper\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetEntryFeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', MoneyType::class, [
            ])
            ->add('max', MoneyType::class, [
                'required' => false,
            ])
            ->add('fee', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssetEntryFee::class,
        ]);
    }
}
