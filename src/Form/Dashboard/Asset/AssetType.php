<?php

namespace App\Form\Dashboard\Asset;

use App\Entity\Person\Contractor;
use App\Entity\Person\Engineer;
use App\Entity\Resource\Asset as Entity;
use App\Enum\AssetStatus;
use App\Enum\AssetType as EnumAssetType;
use App\Form\Dashboard\ResourceType;
use App\Form\Helper\MoneyType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetType extends ResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('bedrooms', NumberType::class, [])
            ->add('bathrooms', NumberType::class, [])
            ->add('assetType', ChoiceType::class, [
                'label' => 'Asset type',
                'choices' => EnumAssetType::values(),
                'choice_label' => function (EnumAssetType $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?EnumAssetType $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('assetStatus', ChoiceType::class, [
                'label' => 'Status',
                'choices' => AssetStatus::values(),
                'choice_label' => function (AssetStatus $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?AssetStatus $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('engineer', EntityType::class, [
                'required' => false,
                'class' => Engineer::class,
                'label' => 'Select a Engineer',
                'placeholder' => '',
                'attr' => [
                    // 'class'            => 'select2-on'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('e')
                        ->join('e.user', 'u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('contractor', EntityType::class, [
                'required' => false,
                'class' => Contractor::class,
                'label' => 'Select a Contractor',
                'placeholder' => '',
                'attr' => [
                    // 'class'            => 'select2-on'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('e')
                        ->join('e.user', 'u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('marketValue', MoneyType::class, [
                'label' => 'Market value',
            ])
            ->add('assetEquities', CollectionType::class, [
                'label' => 'Market Values',
                'entry_type' => AssetEquityType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('birthday', DateType::class, [
                'required' => false,
                'label' => 'Birthday',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Entity::class,
            ]);
    }
}
