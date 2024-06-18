<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

class WebsiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Website $object */
        $object = $options['data'];
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Name'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Description'
            ])
            ->add('phone', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '+216 55******'],
                'required' => false,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Phone number',
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'label_html' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Address'
            ])
            ->add('url', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Website URL',
                'constraints' => [
                    new URl([
                        'message' => 'Please enter a valid URL',
                    ])
                ]
            ])
            ->add('DevMod', CheckboxType::class, [
                'label' => 'Dev Mod',
                'required' => false,
                'label_html'  => true,
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ])
            ->add('isEnabled', CheckboxType::class, [
                'label' => 'Sites enabled',
                'required' => false,
                'label_html'  => true,
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Website::class,
        ]);
    }
}
