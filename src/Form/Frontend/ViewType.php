<?php

namespace App\Form\Frontend;

use App\Entity\Person\Agent;
use App\Entity\Resource\Accommodation;
use App\Entity\Resource\View;
use App\Service\ScheduleService;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

class ViewType extends AbstractType
{
    /**
     * @var ScheduleService
     */
    private $service;

    public function __construct(ScheduleService $service)
    {
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $livingInfo = (bool) $options['livingInfo'];

        $now = new DateTime();
        $now->setTime(8, 0, 0);

        $view = $options['data'];
        $resource = $view->getResource();
        $constraint = null;

        if ($resource instanceof Accommodation) {
            $strTenant = (1 === $resource->getLetAvailableFor()) ? 'tenant' : 'tenants';
            $constraint = [
                new NotNull(),
                new LessThanOrEqual([
                    'value' => $resource->getLetAvailableFor(),
                    'message' => "Sorry, this property is available for up to {$resource->getLetAvailableFor()} $strTenant",
                ]),
            ];
        } else {
            $constraint = [
                new NotNull(),
                new GreaterThan(0),
            ];
        }

        $agent = $resource->getAgent();

        $days = [];
        if ($agent) {
            $days = $this->service->getNext30AvailableDaysFromNow($agent);
            $days = $this->service->getUniqueDaysAsString($days);
        }

        $dates = [];
        foreach ($days as $day) {
            $dates[$day] = $day;
        }

        if ($livingInfo) {
            $builder
                ->add('moveInDate', DateType::class, [
                    'label' => 'Date wish to move in',
                    'widget' => 'single_text',
                    'constraints' => [
                        new NotNull(),
                        new GreaterThan($now),
                    ],
                ])
                ->add('peopleCount', IntegerType::class, [
                    'label' => 'How many people want to move in?',
                    'constraints' => $constraint,
                    'attr' => [
                        'min' => 1,
                    ],
                ]);
        }

        $builder
            ->add('date', ChoiceType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotNull(),
                ],
                'choices' => $dates,
                'placeholder' => '',
                'choice_label' => function ($value) {
                    return DateTime::createFromFormat('Y-m-d', $value)->format('d M Y');
                },
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($agent) {
                $form = $event->getForm();
                $date = $form->get('date')->getData();

                $this->addTime($form, $agent, $date);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($agent) {
                $form = $event->getForm();
                $data = $event->getData();
                $date = $data['date'] ?? null;
                $this->addTime($form, $agent, $date);
            });
    }

    public function addTime(Form $form, ?Agent $agent, string $date = null)
    {
        $times = [];

        if ($agent && $date) {
            $dt = DateTime::createFromFormat('Y-m-d', $date);
            $rs = $this->service->getAvailableTimesFromAgent($agent, $dt);
            $rs = $this->service->getUniqueTimesAsString($rs);

            foreach ($rs as $time) {
                $times[$time] = $time;
            }
        }

        $form
            ->add('time', ChoiceType::class, [
                'mapped' => false,
                'choices' => $times,
                'constraints' => [
                    new NotNull(),
                ],
                'placeholder' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => View::class,
            'csrf_protection' => false,
            'livingInfo' => true,
        ])
            ->setRequired([
                'resource',
            ]);
    }
}
