<?php

namespace App\Form\Dashboard\Asset;

use App\Enum\AssetStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SearchAssetType.
 */
class SearchAssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => AssetStatus::values(),
                'choice_label' => function (AssetStatus $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?AssetStatus $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
