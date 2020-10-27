<?php

namespace App\Form\Dashboard\Asset;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SearchTransactionType.
 */
class SearchTransactionType extends AbstractType
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
        $builder->add('email', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'User e-mail',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
