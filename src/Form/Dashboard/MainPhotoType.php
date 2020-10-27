<?php

namespace App\Form\Dashboard;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;

class MainPhotoType extends AbstractType
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
        $replaces = ['[', ']'];
        $index = str_replace($replaces, '', $options['property_path']);

        $builder->add('file', FileType::class, [
            'required' => false,
            'mapped' => false,
            'constraints' => [
                new File([
                    'maxSize' => '5120k',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                    ],
                    'mimeTypesMessage' => 'Only files with the extension jpeg, jpg and png are allowed',
                    'maxSizeMessage' => 'File too large, maximum allowed size is 5MB',
                    'uploadIniSizeErrorMessage' => 'File too large, maximum allowed size is 5MB',
                        ]),
            ],
        ])->add('photo', HiddenType::class, [
            'required' => false,
            'mapped' => false,
        ]);

        $self = $this;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $e) use ($self, $index) {
            $photo = $e->getData();
            $form = $e->getForm();
            $path = $photo ? $photo->getPath() : null;

            $self->addPathInput($form, $path, $index);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $e) use ($self, $index) {
            $foto = $e->getData();
            $form = $e->getForm();

            $path = $foto ? $foto['path'] : null;

            $self->addPathInput($form, $path, $index);
        });
    }

    private function addPathInput(Form $form, $path, $index)
    {
        $form->add('mainPhoto', HiddenType::class, [
            'required' => false,
            'label' => false,
        ]);
    }
}
