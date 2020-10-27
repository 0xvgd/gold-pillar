<?php

namespace App\Form\Dashboard;

use App\Entity\Resource\Resource;
use App\Enum\RemovalReason;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoveResourceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('removalReason', ChoiceType::class, [
                'label' => 'Why am I going to remove the post?',
                'required' => true,
                'choices' => RemovalReason::values(),
                'choice_label' => function (RemovalReason $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?RemovalReason $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('removalDetails');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
        ]);
    }
}
