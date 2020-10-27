<?php

namespace App\Form\Frontend;

use App\Entity\Reserve\Reserve;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class ReserveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Accommodation */
        $peopleCount = $options['peopleCount'];
        $strTenant = 1 === $peopleCount ? 'tenant' : 'tenants';
        $builder
        ->add('orderDetail', OrderDetailType::class, [
            'mapped' => false,
        ])
            ->add('people', CollectionType::class, [
                'entry_type' => ReservePersonType::class,
                'by_reference' => false,
                'allow_add' => true,
                'constraints' => [
                    new Count(['min' => 1]),
                    new Count([
                        'max' => $peopleCount,
                        'maxMessage' => "Sorry, this property is available for up to $peopleCount $strTenant",
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reserve::class,
        ])
            ->setRequired([
                'peopleCount',
            ]);
    }
}
