<?php

namespace App\Form\Frontend;

use App\Entity\Negotiation\Offer;
use App\Entity\Resource\Accommodation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Accommodation */
        $accommodation = $options['accommodation'];
        $strTenant = 1 === $accommodation->getLetAvailableFor() ? 'tenant' : 'tenants';
        $builder
            ->add('offerValue', NumberType::class, [
                'scale' => 2,
                'grouping' => false,
                'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_HALF_UP,
                'compound' => false,
                'attr' => [
                    'placeholder' => 'Offer value',
                ],
            ])
            ->add('peopleCount', IntegerType::class, [
                'label' => 'How many people want to move in?',
                'constraints' => [
                    new NotNull(),
                    new GreaterThan(0),
                    new LessThanOrEqual([
                        'value' => $accommodation->getLetAvailableFor(),
                        'message' => "Sorry, this property is available for up to {$accommodation->getLetAvailableFor()} $strTenant",
                    ]),
                ],
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
            ]);

        $builder->get('offerValue')
            ->addViewTransformer(new CallbackTransformer(
                function ($offerValue) {
                    return $offerValue;
                },
                function ($offerValue) {
                    $value = str_replace(',', '', $offerValue);

                    return str_replace('.', ',', $value);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ])
        ->setRequired([
            'accommodation',
        ]);
    }
}
