<?php

namespace App\Form\Dashboard;

use App\Entity\Translation\Translation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class TranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['locale'];

        $builder
        ->add('source', TextType::class, [
            'label' => 'Source',
            'required' => true,
            'attr' => [
                'readonly' => $locale && !$locale->getDefaultLocale(),
            ],
            'constraints' => [
                new NotNull(
                    [
                        'message' => 'Enter source',
                    ]
                ),
            ],
        ])
        ->add('target', TextType::class, [
            'label' => 'Target',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Translation::class,
        ])->setRequired([
            'locale',
        ]);
    }
}
