<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html'  => true,
                'label_attr'  => ['class' => "form-label"],
                'label' => 'Username'
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html'  => true,
                'label_attr'  => ['class' => "form-label"],
                'label' => 'First name'
            ])
            ->add('lastName',TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html'  => true,
                'label_attr'  => ['class' => "form-label"],
                'label' => 'Last name'
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '+216 55******'],
                'required' => false,
                'label_html'  => true,
                'label_attr'  => ['class' => "form-label"],
                'label' => 'Your phone',
                'constraints' => []
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
