<?php

namespace App\Form\Helper;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

class PhotoType extends AbstractType
{
    public function getParent()
    {
        return HiddenType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['aspect_ratio'] = $options['aspect_ratio'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'aspect_ratio' => null,
                'constraints' => [
                    new Url([
                        'message' => 'Please choose a file',
                    ]),
                ],
            ]);
    }

    public function getBlockPrefix()
    {
        return 'app_photo';
    }
}
