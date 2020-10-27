<?php

namespace App\Form\Dashboard;

use App\Entity\Person\Agent;
use App\Enum\PostStatus;
use App\Form\Helper\AddressType;
use App\Form\Helper\PhotoType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

abstract class ResourceType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker = null;

    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'constraints' => [
                    new NotNull(
                        [
                            'message' => 'Enter name',
                        ]
                    ),
                ],
            ])
            ->add('description', TextareaType::class)
            ->add('youtubeUrl', UrlType::class, [
                'label' => 'Youtube url',
                'required' => false,
            ])
            ->add('tag', TextType::class, [
                'label' => 'Tag',
                'required' => false,
            ])
            ->add('mainPhoto', PhotoType::class, [
                'required' => true,
                'aspect_ratio' => 17 / 10,
                'constraints' => [
                    new NotNull([
                        'message' => 'Please choose the main photo',
                    ]),
                ],
            ])
            ->add('documents', CollectionType::class, [
                'block_prefix' => 'resource_documents',
                'entry_type' => DocumentType::class,
                'entry_options' => [
                    'block_prefix' => 'resource_documents_entry',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('photos', CollectionType::class, [
                'block_prefix' => 'resource_photos',
                'entry_type' => PhotoType::class,
                'entry_options' => [
                    'aspect_ratio' => 16 / 9,
                    'block_prefix' => 'resource_photos_entry',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Select at least one photo',
                    ]),
                ],
            ])
            ->add('floorplans', CollectionType::class, [
                'block_prefix' => 'resource_photos',
                'entry_type' => PhotoType::class,
                'entry_options' => [
                    'aspect_ratio' => 16 / 9,
                    'block_prefix' => 'resource_photos_entry',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('address', AddressType::class, [
                'block_prefix' => 'resource_address',
                'constraints' => [
                    new Valid(),
                ],
            ]);

        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('postStatus', ChoiceType::class, [
                    'label' => 'Post status',
                    'choices' => PostStatus::values(),
                    'choice_label' => function (PostStatus $enum) {
                        return $enum->getLabel();
                    },
                    'choice_value' => function (?PostStatus $enum) {
                        return $enum ? $enum->getValue() : null;
                    },
                ])
                ->add('agent', EntityType::class, [
                    'required' => false,
                    'class' => Agent::class,
                    'label' => 'Select a Agent',
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
                ]);
        }
    }
}
