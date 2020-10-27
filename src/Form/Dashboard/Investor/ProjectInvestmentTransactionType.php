<?php

namespace App\Form\Dashboard\Investor;

use App\Entity\Finance\ProjectInvestmentTransaction as Entity;
use App\Form\Helper\MoneyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class ProjectInvestmentTransactionType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker = null;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $availableMargin = $options['availableMargin'];

        $builder
            ->add('amount', MoneyType::class, [
                'label' => false,
                'currency' => 'GBP',
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => $availableMargin,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'availableMargin',
            ])
            ->setDefaults([
                'data_class' => Entity::class,
            ]);
    }
}
