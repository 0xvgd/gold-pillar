<?php

namespace App\Form\Dashboard;

use App\Entity\Person\Engineer;
use App\Entity\Security\User;
use App\Form\DataTransform\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EngineerType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Select a Engineer',
                'placeholder' => 'Select Engineer',
                'attr' => [
                    'class' => 'select2-on',
                ],
                'query_builder' => function (EntityRepository $er) {
                    // TODO - criar entidade Engineer
                    return $er
                        ->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
            ]);

        //$builder->get('user')->addModelTransformer(new UserTransformer($this->em));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Engineer::class,
        ]);
    }
}
