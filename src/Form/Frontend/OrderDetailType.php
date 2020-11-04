<?php

namespace App\Form\Frontend;

use App\Entity\Reserve\Reserve;
use App\Form\Helper\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class OrderDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', AddressType::class, [
                'mapped' => false,
                'constraints' => [new Valid()],
            ])
            ->add('cardName', TextType::class, [
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ])
            ->add('cardNumber', TextType::class, [
                'mapped' => false,
            ])
            ->add('cv2', TextType::class, [
                'label' => 'CVV',
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ])
            ->add('expiryDateMonth', TextType::class, [
                'label' => 'Exp. Month',
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ])
            ->add('expiryDateYear', TextType::class, [
                'label' => 'Exp. Year',
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ])
            ->add('hashDigest', HiddenType::class, [
                'attr' => [
                    'data-field' => 'hashDigest',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('transactionDateTime', HiddenType::class, [
                'attr' => [
                    'data-field' => 'transactionDateTime',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('callbackURL', HiddenType::class, [
                'attr' => [
                    'data-field' => 'callbackURL',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('orderID', HiddenType::class, [
                'attr' => [
                    'data-field' => 'orderID',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('orderDescription', HiddenType::class, [
                'attr' => [
                    'data-field' => 'orderDescription',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('currencyCode', HiddenType::class, [
                'attr' => [
                    'data-field' => 'currencyCode',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('fullAmount', HiddenType::class, [
                'attr' => [
                    'data-field' => 'fullAmount',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('amount', HiddenType::class, [
                'attr' => [
                    'data-field' => 'amount',
                ],
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reserve::class,
        ]);
    }
}
