<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\WebsiteConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class WebsiteConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tag', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Tag',
                'constraints' => [

                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Description'
            ])
            ->add('value', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Value',
            ])
            ->add('defaultLoad', ChoiceType::class, [
                'label' => 'Default load',
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'choices' => [
                    'Please select' => 0,
                    'Footer' => 1,
                    'Header' => 2,
                    'Body' => 3,
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('isEnabled', CheckboxType::class, [
                'label' => 'Category enabled',
                'required' => false,
                'label_html'  => true,
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ])
            ->add('isDeleted', ButtonType::class, [
                'label' => 'Supprimer',
                'attr' => [
                    'class' => 'btn btn-danger', // Add any additional classes here for styling
                ],
            ])
            
            ;;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebsiteConfiguration::class,
        ]);
    }
}
