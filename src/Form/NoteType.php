<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Note;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Message'
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('w')
                        ->orderBy('w.label', 'ASC');
                },
                'choice_label' => 'label',
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'label_html' => false,
                'label' => 'Status',
                'attr' => [
                    'class' => 'select2 form-control col-12',
                    'placeholder' => 'select status'
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
