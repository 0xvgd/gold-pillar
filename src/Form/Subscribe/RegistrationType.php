<?php

namespace App\Form\Subscribe;

use App\Entity\Plan;
use App\Form\Helper\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $planCode = $options['planCode'];
        $parentInviteCode = $options['parentInviteCode'];

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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [new Length(['min' => 6])],
                'mapped' => false,
                'required' => true,
                'invalid_message' => 'Password confirmation and Password must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                    ],
                ],
                'first_options' => [
                    'icon' => 'fa-lock',
                    'label' => 'Password',
                ],
                'second_options' => [
                    'icon' => 'fa-lock',
                    'label' => 'Password confirmation',
                ],
            ])
            ->add('phone', TelType::class, [
                'icon' => 'fa-phone',
                'mapped' => false,
                'attr' => [
                    'maxlength' => 15,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 15,
                    ]),
                ],
            ])
            ->add('address', AddressType::class, [
                'constraints' => [
                    new Valid(),
                ],
            ]);

        if (Plan::PLAN_AGENT === $planCode || Plan::PLAN_INVESTOR === $planCode) {
            $builder
            ->add('parentInviteCode', TextType::class, [
                'label' => 'Invite code',
                'icon' => 'fa-envelope-open-text',
                'required' => true,
                'mapped' => false,
                'data' => $parentInviteCode,
                'attr' => [
                    'placeholder' => 'Invite code',
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'planCode',
                'parentInviteCode',
            ]);
    }
}
