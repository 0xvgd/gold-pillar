<?php

namespace App\Form\Dashboard\Application;

use App\Entity\Finance\AssetAccount;
use App\Entity\Finance\ProjectAccount;
use App\Entity\Finance\PropertyAccount;
use App\Entity\Helper\TableKey;
use App\Form\Helper\MoneyType;
use App\Form\Helper\TableKeyHelper;
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
 * CreateNoteType.
 */
class CreateNoteType extends AbstractType
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
        $tableKeyHelper = new TableKeyHelper($this->em);

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
            ->add('accountType', ChoiceType::class, [
                'required' => true,
                'placeholder' => '(Select)',
                'choices' => [
                    'Asset account (AT)' => 'asset',
                    'Company account (CP)' => 'company',
                    'Project account (PJ)' => 'project',
                    'Property account (PP)' => 'property',
                ],
            ])
            ->add('assetAccount', EntityType::class, [
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
            ->add(
                'assetTransactionType',
                EntityType::class,
                $tableKeyHelper->formOptions(
                    TableKey::ASSET_TRANSACTION_TYPE,
                    'Transaction type',
                    false
                )
            )
            ->add('projectAccount', EntityType::class, [
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
            ->add(
                'projectTransactionType',
                EntityType::class,
                $tableKeyHelper->formOptions(
                    TableKey::PROJECT_TRANSACTION_TYPE,
                    'Transaction type',
                    false
                )
            )
            ->add('propertyAccount', EntityType::class, [
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
            ->add(
                'propertyTransactionType',
                EntityType::class,
                $tableKeyHelper->formOptions(
                    TableKey::TRANSACTION_TYPE,
                    'Transaction type',
                    false
                )
            )
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
