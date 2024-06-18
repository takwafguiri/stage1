<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html'  => true,
                'label_attr'  => ['class' => "form-label"],
                'label' => 'Current password',
                'constraints' => [
                    new UserPassword(null, "Wrong password provided" )
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'attr' => ['class' => 'form-control', 'placeholder' => '+216 55******'],
                'first_options'  => ['label' => 'Password', 'label_html'  => true, 'label_attr'  => ['class' => "form-label"], 'attr' => ['class' => 'form-control']],
                'second_options' => ['label' => 'Confirm Password', 'label_html'  => true, 'label_attr'  => ['class' => "form-label"], 'attr' => ['class' => 'form-control']],
                'required' => true,
                'constraints' => [
                    new PasswordStrength(minScore: PasswordStrength::STRENGTH_WEAK, message:  'Your password is too easy to guess. it should be at least 10 characters, digits and symboles')
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
