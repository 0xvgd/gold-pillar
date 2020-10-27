<?php

namespace App\Form\Security;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * UserType.
 */
class UserType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'User name',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
