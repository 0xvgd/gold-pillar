<?php

namespace App\Form;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Select2Type extends AbstractType
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addViewTransformer(new CallbackTransformer(
                function ($entity) use ($options) {
                    $data = [];
                    if (null === $entity) {
                        return $data;
                    }
                    $accessor = PropertyAccess::createPropertyAccessor();
                    $text = is_null($options['choice_label'])
                        ? (string) $entity
                        : $accessor->getValue($entity, $options['choice_label']);

                    $data[$accessor->getValue($entity, 'id')] = $text;

                    return $data;
                },
                function ($id) use ($options) {
                    $em = $this->registry->getManagerForClass($options['class']);

                    return $em->find($options['class'], $id);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['class'])
            ->setDefaults([
                'choice_label' => null,
                'compound' => false,
            ])
            ->setAllowedTypes('choice_label', ['null', 'string']);
    }

    public function getBlockPrefix(): string
    {
        return 'select2';
    }
}
