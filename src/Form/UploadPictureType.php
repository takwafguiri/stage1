<?php

// src/Form/Type/UploadPictureType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('picture', FileType::class, [
                'label' => 'Upload Picture',
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'extensions' => ['jpeg', 'jpg', 'png'],
                        'extensionsMessage' => 'Please upload a valid image file',
                    ]),
                ],
                // Add more options as needed
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}