<?php

namespace App\Form\Dashboard;

use App\Entity\Person\Agent;
use App\Entity\Security\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class AgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Contractor user',
                'placeholder' => 'Contractor user',
                'attr' => [
                    'class' => 'select2-on',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Birth date',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('experience', DateType::class, [
                'label' => 'Agent since (experience)',
                'widget' => 'single_text',
                'required' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
        ]);
    }
}
