<?php

namespace App\Form\Dashboard;

use App\Entity\Helper\TableKey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * TableKeyType.
 */
class TableKeyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', NumberType::class, [
            'label' => 'Id',
            'attr' => [
                'placeholder' => 'Id',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('description', TextType::class, [
            'label' => 'Description',
            'attr' => [
                'placeholder' => 'Description',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('tableValues', CollectionType::class, [
            'label' => 'Values',
            'entry_type' => TableValueType::class,
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TableKey::class,
        ]);
    }
}
