<?php

namespace App\Form\Subscribe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AgentPreRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'icon' => 'fa-user',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Full name',
                ],
            ])
            ->add('email', RepeatedType::class, [
                'label' => 'E-mail',
                'type' => EmailType::class,
                'invalid_message' => 'Email address does not match.',
                'required' => true,
                'first_options' => [
                    'icon' => 'fa-envelope',
                    'label' => 'E-mail address',
                ],
                'second_options' => [
                    'icon' => 'fa-envelope',
                    'label' => 'Confirm e-mail address',
                ],
                'attr' => [
                    'placeholder' => '',
                ],
            ])
            ->add('phone', TelType::class, [
                'icon' => 'fa-phone',
                'mapped' => true,
                'attr' => [
                    'maxlength' => 15,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 15,
                    ]),
                ],
            ]);
    }
}
