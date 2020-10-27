<?php

namespace App\Form\Dashboard\Resource;

use App\Entity\Finance\Recurring;
use App\Enum\PeriodUnit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RecurringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'constraints' => [
                    new Length(['max' => 150]),
                ],
            ])
            ->add('amount', MoneyType::class)
            ->add('time', TimeType::class)
            ->add('interval', ChoiceType::class, [
                'choices' => [
                    PeriodUnit::DAY(),
                    PeriodUnit::WEEK(),
                    PeriodUnit::MONTH(),
                    PeriodUnit::YEAR(),
                ],
                'choice_attr' => function (PeriodUnit $unit) {
                    return [
                        'data-unit' => $unit->getValue(),
                    ];
                },
                'choice_value' => function (?PeriodUnit $unit) {
                    return $unit ? $unit->getValue() : null;
                },
                'choice_label' => function (PeriodUnit $unit) {
                    if ($unit->equals(PeriodUnit::DAY())) {
                        return 'Daily';
                    }
                    if ($unit->equals(PeriodUnit::WEEK())) {
                        return 'Weekly';
                    }
                    if ($unit->equals(PeriodUnit::MONTH())) {
                        return 'Monthly';
                    }
                    if ($unit->equals(PeriodUnit::YEAR())) {
                        return 'Yearly';
                    }
                },
            ])
            ->add('dayOfWeek', ChoiceType::class, [
                'property_path' => 'dayOrMonth',
                'choices' => [
                    'Monday' => 1,
                    'Tuesday' => 2,
                    'Wednesday' => 3,
                    'Thursday' => 4,
                    'Friday' => 5,
                    'Saturday' => 6,
                    'Sunday' => 7,
                ],
            ])
            ->add('dayOfMonth', ChoiceType::class, [
                'property_path' => 'dayOrMonth',
                'choices' => range(1, 31),
                'choice_label' => function ($day) {
                    if ($day > 0) {
                        if ($day > 3) {
                            $day .= 'th';
                        } elseif (1 == $day) {
                            $day .= 'st';
                        } elseif (2 == $day) {
                            $day .= 'nd';
                        } elseif (3 == $day) {
                            $day .= 'rd';
                        }
                    }

                    return $day;
                },
            ])
            ->add('monthOfYear', ChoiceType::class, [
                'property_path' => 'dayOrMonth',
                'choices' => [
                    'January' => 1,
                    'February' => 2,
                    'March' => 3,
                    'April' => 4,
                    'May' => 5,
                    'June' => 6,
                    'July' => 7,
                    'August' => 8,
                    'September' => 9,
                    'October' => 10,
                    'November' => 11,
                    'December' => 12,
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $interval = $data->getInterval()->getValue();
                $keys = [
                    'week' => 'dayOfWeek',
                    'month' => 'dayOfMonth',
                    'year' => 'monthOfYear',
                ];
                $value = 1;
                if (isset($keys[$interval])) {
                    $key = $keys[$interval];
                    $value = $form->get($key)->getData();
                }
                $data->setDayOrMonth($value);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recurring::class,
        ]);
    }
}
