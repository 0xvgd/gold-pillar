<?php

namespace App\Form\Frontend;

use App\Entity\Person\Agent;
use App\Entity\Resource\Property;
use App\Entity\Resource\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class SellingForm extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', MoneyType::class, [
                'currency' => 'GBP',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('postcode', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('street', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('town', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('county', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('agent', EntityType::class, [
                'class' => Agent::class,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $agent = $form->get('agent')->getData();

                $this->addView($form, $agent);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $agentId = $data['agent'] ?? null;
                $agent = null;
                if ($agentId) {
                    $agent = $this->em->find(Agent::class, $agentId);
                }

                $this->addView($form, $agent);
            })
        ;
    }

    public function addView(Form $form, ?Agent $agent)
    {
        $resource = new Property();
        $resource->setAgent($agent);
        $view = new View();
        $view->setResource($resource);

        $form
            ->add('view', ViewType::class, [
                'livingInfo' => false,
                'resource' => $resource,
                'data' => $view,
                'constraints' => [
                    new Valid(),
                ],
            ]);
    }
}
