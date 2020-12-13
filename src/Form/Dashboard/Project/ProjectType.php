<?php

namespace App\Form\Dashboard\Project;

use App\Entity\Person\Broker;
use App\Entity\Person\Contractor;
use App\Entity\Person\Engineer;
use App\Entity\Resource\Project as Entity;
use App\Enum\ProjectStatus;
use App\Enum\ProjectType as EnumProjectType;
use App\Form\Dashboard\ResourceType;
use App\Form\Helper\MoneyType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectType extends ResourceType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        parent::__construct($authChecker);

        $this->authChecker = $authChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $isAdmin = $this->authChecker->isGranted('ROLE_ADMIN');

        $builder
            ->add('contractor', EntityType::class, [
                'disabled' => !$isAdmin,
                'required' => false,
                'class' => Contractor::class,
                'label' => 'Select a Contractor',
                'placeholder' => '',
                'attr' => [
                    'class' => 'select2-on',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('e')
                        ->join('e.user', 'u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('broker', EntityType::class, [
                'disabled' => !$isAdmin,
                'required' => false,
                'class' => Broker::class,
                'label' => 'Select a Broker',
                'placeholder' => '',
                'attr' => [
                    'class' => 'select2-on',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('e')
                        ->join('e.user', 'u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('salePriceProjection', MoneyType::class, [
            ])
            ->add('constructionCost', MoneyType::class, [
            ])
            ->add('engineer', EntityType::class, [
                'disabled' => !$isAdmin,
                'required' => false,
                'class' => Engineer::class,
                'label' => 'Select a Engineer',
                'placeholder' => '',
                'attr' => [
                    'class' => 'select2-on',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('e')
                        ->join('e.user', 'u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('saleRealPrice', MoneyType::class, [
                'required' => false,
            ])
            ->add('projectType', ChoiceType::class, [
                'label' => 'Project Type',
                'choices' => EnumProjectType::values(),
                'choice_label' => function (EnumProjectType $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?EnumProjectType $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('projectStatus', ChoiceType::class, [
                'label' => 'Project Status',
                'choices' => ProjectStatus::values(),
                'choice_label' => function (ProjectStatus $enum) {
                    return $enum->getLabel();
                },
                'choice_value' => function (?ProjectStatus $enum) {
                    return $enum ? $enum->getValue() : null;
                },
            ])
            ->add('purchasePrice', MoneyType::class, [])
            ->add('bedrooms', NumberType::class, [])
            ->add('bathrooms', NumberType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Entity::class,
            ]);
    }
}
