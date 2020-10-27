<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hash = $options['hash'];

        $builder
            ->add('hash', HiddenType::class, [
                'data' => $hash,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => true,
                'invalid_message' => 'Password do not match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                    ],
                ],
                'first_options' => [
                    'label' => 'Password',
                ],
                'second_options' => [
                    'label' => 'Password confirmation',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'hash',
            ]);
    }
}
