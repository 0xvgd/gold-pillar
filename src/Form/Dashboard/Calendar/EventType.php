<?php

namespace App\Form\Dashboard\Calendar;

use App\Entity\Calendar\Event;
use App\Enum\CalendarEventType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    CalendarEventType::BUSINESS(),
                    CalendarEventType::PERSONAL(),
                ],
                'choice_label' => function (CalendarEventType $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?CalendarEventType $enum) {
                    return $enum ? $enum->getValue() : null;
                },
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('allDay', CheckboxType::class, [
                'required' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $allDay = $data && $data->isAllDay();

                $this->addTimes($form, $allDay);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $allDay = $data['allDay'] ?? false;

                $this->addTimes($form, $allDay);
            })
        ;
    }

    public function addTimes(Form $form, bool $allDay)
    {
        $constraints = [];
        if (!$allDay) {
            $constraints = [
                new NotBlank(),
            ];
        }

        $form
            ->add('startTime', TimeType::class, [
                'constraints' => $constraints,
                'widget' => 'single_text',
                'input_format' => 'H:i',
            ])
            ->add('endTime', TimeType::class, [
                'constraints' => $constraints,
                'widget' => 'single_text',
                'input_format' => 'H:i',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => false,
        ]);
    }
}
