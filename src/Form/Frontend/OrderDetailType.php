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

        // <input type='hidden' name='MerchantID' value='GOLDPI-8112142'/>
        // <input type='hidden' name='Amount' value='9132'/>
        // <input type='hidden' name='FullAmount' value='91.32'/>
        // <input type='hidden' name='CurrencyCode' value='826'/>
        // <input type='hidden' name='OrderID' value='1601221561'/>
        // <input type='hidden' name='TransactionType' value='SALE'/>
        // <input type='hidden' name='TransactionDateTime' value='2020-09-27 15:46:01 +00:00'/>
        // <input type='hidden' name='OrderDescription' value='Example order processing | Direct API '/>
        // <input type='hidden' name='CustomerName' value='Geoff Wayne'/>
        // <input type='hidden' name='Address1' value='113 Glendower Road'/>
        // <input type='hidden' name='Address2' value=''/>
        // <input type='hidden' name='Address3' value=''/>
        // <input type='hidden' name='Address4' value=''/>
        // <input type='hidden' name='City' value='Birmingham'/>
        // <input type='hidden' name='State' value='West Midlands'/>
        // <input type='hidden' name='PostCode' value='B42 1SX'/>
        // <input type='hidden' name='CountryCode' value='826'/><
        // input type='hidden' name='HashMethod' value='SHA1'/>
        // <input type='hidden' name='EmailAddress' value=''/>
        // <input type='hidden' name='CallbackURL' value='http://localhost:8001/results.php'/>
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reserve::class,
        ]);
    }
}
