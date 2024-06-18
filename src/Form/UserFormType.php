<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Enum\UserRole;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $object */
        $object = $options['data'];
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Username'
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'First name'
            ])
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Last name'
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '+216 55******'],
                'required' => false,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Phone number',
                'constraints' => []
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role',
                'mapped' => false,
                'data' => $object->getRoles()[0],
                'choices' => array_flip(UserRole::getAllRolesWithLabel()),
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'label_html' => true,
                'attr' => [
                    'class' => 'form-select',
                    'data-custom-attribute' => 'custom-value',
                ]])
            // Your form builder code
            ->add('Password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options' => ['label' => 'Password', 'label_html' => true, 'label_attr' => ['class' => "form-label"], 'attr' => ['class' => 'form-control']],
                'second_options' => ['label' => 'Confirm Password', 'label_html' => true, 'label_attr' => ['class' => "form-label"], 'attr' => ['class' => 'form-control']],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters'
                    ]),
                ]])
            ->add('websites', EntityType::class, [
            'class' => Website::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('w')
                    ->orderBy('w.name', 'ASC');
            },
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => false,
            'required' => false,
            'label' => 'Websites',
            'attr' => [
                'class' => 'select2 form-control',
                'placeholder' => 'select websites'
            ]])
            ->add('isEnabled', CheckboxType::class, [
            'label' => 'User enabled',
            'required' => false,
            'label_html' => true,
            'attr' => [
                'class' => 'form-check-input'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}