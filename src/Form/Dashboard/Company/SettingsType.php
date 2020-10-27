<?php

namespace App\Form\Dashboard\Company;

use App\Entity\Company\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SettingsType.
 */
class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commissionRate', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('companyFee', PercentType::class, [
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('projectAdvertisingReserve', PercentType::class, [
                'label' => ' Advertising Reserve',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('projectAgentEarnings', PercentType::class, [
                'label' => 'Agent Earnings',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('projectContractorEarnings', PercentType::class, [
                'label' => 'Contractor Earnings',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('projectEngineerEarnings', PercentType::class, [
                'label' => 'Engineer Earnings',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('projectBrokerEarnings', PercentType::class, [
                'label' => 'Broker Earnings',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('assetEntryFee', PercentType::class, [
                'label' => 'Entry fee',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('assetExitFee', PercentType::class, [
                'label' => 'Exit fee',
                'scale' => 2,
                'type' => 'fractional',
            ])
            ->add('monthlyFee', NumberType::class, [
                'scale' => 2,
                'grouping' => false,
                'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_HALF_UP,
                'compound' => false,
                'attr' => [
                    'placeholder' => 'Monthly Fee',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
            'block_prefix' => 'settings',
        ]);
    }
}
