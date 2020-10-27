<?php

namespace App\Form\Schedule;

use App\Entity\Schedule\Day;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;

class DayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('weekDay', HiddenType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new Range(['min' => 1, 'max' => 7]),
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
            ])
            ->add('startTime', TimeType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('endTime', TimeType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('max', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new GreaterThan(0),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Day::class,
        ]);
    }
}
