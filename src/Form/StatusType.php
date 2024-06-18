<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Label'
            ])->add('colorCode', ColorType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Color'
            ])
            ->add('isEnabled', CheckboxType::class, [
                'label' => 'Status enabled',
                'required' => false,
                'label_html'  => true,
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Status::class,
        ]);
    }
}
