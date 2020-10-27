<?php

namespace App\Form\Dashboard\Asset;

use App\Entity\Finance\AssetTransaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isPaid', CheckboxType::class, [
                'label' => 'Payment was made or received',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AssetTransaction::class,
            ]);
    }
}
