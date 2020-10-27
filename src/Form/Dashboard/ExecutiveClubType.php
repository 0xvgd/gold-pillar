<?php

namespace App\Form\Dashboard;

use App\Entity\ExecutiveClub;
use App\Form\Helper\PhotoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExecutiveClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('minSales', NumberType::class)
            ->add('maxSales', NumberType::class, [
                'required' => false,
            ])
            ->add('fee', PercentType::class, [
                'label' => 'Agent take home',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('distributableValue', PercentType::class, [
                'label' => 'Distributable value',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('companyFee', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('pathLogo', PhotoType::class, [
                'required' => true,
                'aspect_ratio' => 16 / 9,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExecutiveClub::class,
        ]);
    }
}
