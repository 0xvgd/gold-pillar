<?php

namespace App\Form\Dashboard\Project;

use App\Entity\Finance\ProjectTransaction;
use App\Entity\Helper\TableKey;
use App\Form\Helper\MoneyType;
use App\Form\Helper\TableKeyHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectTransactionType extends AbstractType
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
        $tableKeyHelper = new TableKeyHelper($this->em);

        $builder
            ->add(
                'type',
                EntityType::class,
                $tableKeyHelper->formOptions(
                    TableKey::PROJECT_TRANSACTION_TYPE,
                    'Project transaction type'
                )
            )
            ->add('description', TextareaType::class)
            ->add('amount', MoneyType::class, [
                'label' => 'Amount',
                'currency' => 'GBP',
            ])
            ->add('isPaid', CheckboxType::class, [
                'label' => 'Payment was made or received',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => ProjectTransaction::class,
            ]);
    }
}
