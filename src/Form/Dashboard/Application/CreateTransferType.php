<?php

namespace App\Form\Dashboard\Application;

use App\Entity\Finance\AssetAccount;
use App\Entity\Finance\ProjectAccount;
use App\Entity\Finance\PropertyAccount;
use App\Form\Helper\MoneyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * CreateTransferType.
 */
class CreateTransferType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projectAccounts = $this->em->createQueryBuilder()
            ->select('pj')
            ->from(ProjectAccount::class, 'pj')
            ->orderBy('pj.id', 'ASC')
            ->getQuery()
            ->getResult();

        $propertyAccounts = $this->em->createQueryBuilder()
            ->select('pp')
            ->from(PropertyAccount::class, 'pp')
            ->orderBy('pp.id', 'ASC')
            ->getQuery()
            ->getResult();

        $assetAccounts = $this->em->createQueryBuilder()
            ->select('at')
            ->from(AssetAccount::class, 'at')
            ->orderBy('at.id', 'ASC')
            ->getQuery()
            ->getResult();

        $builder
        ->add('accountFromType', ChoiceType::class, [
            'label' => 'From',
            'required' => true,
            'placeholder' => '(Select)',
            'choices' => [
                'Asset account (AT)' => 'asset',
                'Company account (CP)' => 'company',
                'Project account (PJ)' => 'project',
                'Property account (PP)' => 'property',
            ],
        ])
            ->add('assetFromAccount', EntityType::class, [
                'label' => 'Account number',
                'choice_label' => 'accountNumber',
                'class' => AssetAccount::class,
                'placeholder' => '(Select)',
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Select',
                    'class' => 'select2ativo',
                ],
                'choices' => $assetAccounts,
            ])
            ->add('projectFromAccount', EntityType::class, [
                'label' => 'Account number',
                'choice_label' => 'accountNumber',
                'class' => ProjectAccount::class,
                'placeholder' => '(Select)',
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Select',
                    'class' => 'select2ativo',
                ],
                'choices' => $projectAccounts,
            ])
            ->add('propertyFromAccount', EntityType::class, [
                'label' => 'Account number',
                'choice_label' => 'accountNumber',
                'class' => PropertyAccount::class,
                'placeholder' => '(Select)',
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Select',
                    'class' => 'select2ativo',
                ],
                'choices' => $propertyAccounts,
            ])
            ->add('accountToType', ChoiceType::class, [
                'label' => 'To',
                'placeholder' => '(Select)',
                'required' => true,
                'choices' => [
                    'Asset account (AT)' => 'assetTo',
                    'Company account (CP)' => 'companyTo',
                    'Project account (PJ)' => 'projectTo',
                    'Property account (PP)' => 'propertyTo',
                ],
            ])
            ->add('assetToAccount', EntityType::class, [
                'label' => 'Account number',
                'choice_label' => 'accountNumber',
                'class' => AssetAccount::class,
                'placeholder' => '(Select)',
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Select',
                    'class' => 'select2ativo',
                ],
                'choices' => $assetAccounts,
            ])
            ->add('projectToAccount', EntityType::class, [
                'label' => 'Account number',
                'choice_label' => 'accountNumber',
                'class' => ProjectAccount::class,
                'placeholder' => '(Select)',
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Select',
                    'class' => 'select2ativo',
                ],
                'choices' => $projectAccounts,
            ])
            ->add('propertyToAccount', EntityType::class, [
                'label' => 'Account number',
                'choice_label' => 'accountNumber',
                'class' => PropertyAccount::class,
                'placeholder' => '(Select)',
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Select',
                    'class' => 'select2ativo',
                ],
                'choices' => $propertyAccounts,
            ])
            ->add('dateRef', DateType::class, [
                'required' => false,
                'label' => 'Date',
                'widget' => 'single_text',
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Amount',
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Value must be greater than zero', ]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
