<?php

namespace App\Form\Dashboard\Application;

use App\Entity\ApplicationSettings;
use App\Form\Helper\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AccountType.
 */
class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commissionRate', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('companyFee', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('monthlyFee', MoneyType::class, [
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ApplicationSettings::class,
        ]);
    }
}
