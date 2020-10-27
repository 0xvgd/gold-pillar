<?php

namespace App\Form\Dashboard;

use App\Entity\Translation\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['data'];

        $builder
            ->add('code', TextType::class, [
                'attr' => [
                    'oninput' => "this.value=this.value.toLowerCase().replace(/[^A-z0-9\-]/g, '')",
                ],
            ])
            ->add('defaultLocale', CheckboxType::class, [
                'label' => 'Default',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom'],
            ])
            ->add('translations', CollectionType::class, [
                'block_prefix' => 'locale_translations',
                'entry_type' => TranslationType::class,
                'entry_options' => [
                    'block_prefix' => 'locale_translations_entry',
                    'locale' => $locale,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
         ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Locale::class,
        ]);
    }
}
