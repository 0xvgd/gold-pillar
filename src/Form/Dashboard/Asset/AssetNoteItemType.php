<?php

namespace App\Form\Dashboard\Asset;

use App\Entity\Helper\TableKey;
use App\Form\Helper\MoneyType;
use App\Form\Helper\TableKeyHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * AssetNoteItemType.
 */
class AssetNoteItemType extends AbstractType
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

        $builder
            ->add(
                'transactionType',
                EntityType::class,
                $tableKeyHelper->formOptions(
                    TableKey::ASSET_TRANSACTION_TYPE,
                    'Transaction type',
                    true
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
                        'message' => 'Value must be greater than zero',
                    ]),
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
