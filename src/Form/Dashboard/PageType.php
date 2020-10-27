<?php

namespace App\Form\Dashboard;

use App\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'oninput' => "this.value=this.value.toLowerCase().replace(/[^A-z0-9\-]/g, '')",
                ],
            ])
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 150]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotNull(),
                    new Length(['max' => 5000]),
                ],
            ])
            ->add('youtubeUrl', UrlType::class, [
                'label' => 'Youtube url',
                'required' => false,
            ])
            ->add('banners')
        ;

        $builder
            ->get('banners')
            ->addModelTransformer(new CallbackTransformer(
                function ($data) {
                    return json_encode($data);
                },
                function ($data) {
                    return json_decode($data, true);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
