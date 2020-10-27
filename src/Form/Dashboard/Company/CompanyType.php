<?php

namespace App\Form\Dashboard\Company;

use App\Entity\Company\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * CompanyType.
 */
class CompanyType extends AbstractType
{
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
            ->add('defaultCompany', CheckboxType::class, [
                'label' => 'Default',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom'],
            ])
            ->add('settings', SettingsType::class, [
                'block_prefix' => 'company_settings',
                'constraints' => [
                    new Valid(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
