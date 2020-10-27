<?php

namespace App\Form\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvestorClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $parentInviteCode = $options['parentInviteCode'];

        $builder->add('parentInviteCode', TextType::class, [
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

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
