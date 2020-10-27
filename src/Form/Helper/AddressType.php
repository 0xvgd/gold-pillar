<?php

namespace App\Form\Helper;

use App\Entity\Address;
use Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = Locale::getDefault();

        $builder
            ->add('postcode', TextType::class, [
                'attr' => [
                    'data-field' => 'postcode',
                ],
                'constraints' => [new NotBlank(), new Length(['max' => 14])],
            ])
            ->add('addressLine1', TextType::class, [
                'attr' => [
                    'data-field' => 'addressLine1',
                ],
                'constraints' => [new NotBlank(), new Length(['max' => 64])],
            ])
            ->add('addressLine2', TextType::class, [
                'attr' => [
                    'data-field' => 'addressLine2',
                ],
                'required' => false,
                'constraints' => [new Length(['max' => 64])],
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'data-field' => 'city',
                ],
                'constraints' => [new NotBlank(), new Length(['max' => 50])],
            ])
            ->add('town', TextType::class, [
                'attr' => [
                    'data-field' => 'town',
                ],
                'required' => false,
                'constraints' => [new Length(['max' => 50])],
            ])
            ->add('lat')
            ->add('lng')
            ->add('country', CountryType::class, [
                'choice_translation_locale' => $this->getLocale($locale),
                'attr' => [
                    'data-field' => 'country',
                ],
                'preferred_choices' => ['GB'],
                'constraints' => [new NotBlank()],
                'attr' => [
                    'class' => 'select2-on',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'block_prefix' => 'address',
        ]);
    }

    protected function getLocale($locale)
    {
        switch ($locale) {
            case 'br':
                $locale = 'pt-br';
                break;

            default:
                // code...
                break;
        }

        return $locale;
    }
}
