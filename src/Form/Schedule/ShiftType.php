<?php

namespace App\Form\Schedule;

use App\Entity\Schedule\Shift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class ShiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $daysOptions = [
            'entry_type' => DayType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ];

        $empty = $options['auto_initialize'];
        $days = null;
        if ($empty) {
            $days = Shift::generateDays();
            $daysOptions['data'] = $days;
        }

        $builder
            ->add('duration', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0),
                ],
            ])
            ->add('position', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0),
                ],
            ])
            ->add('days', CollectionType::class, $daysOptions)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shift::class,
        ]);
    }
}
