<?php

namespace App\Form\Dashboard;

use App\Entity\Plan;
use App\Form\Helper\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('code')
            ->add('price', MoneyType::class, [
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Active',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plan::class,
        ]);
    }
}
