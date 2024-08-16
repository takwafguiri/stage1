<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Page;
use Doctrine\ORM\EntityRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Service;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\WebsiteCategory;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedWebsite = $options['selected_website'];
        $builder
            //Menu tab
            ->add('showInMenu', CheckboxType::class, [
                'required' => false,
                'label' => 'Show In Menu',
                'attr' => ['class' => 'form-check-input'],
                'label_html' => true,
            ])
            ->add('displayOrder', IntegerType::class, [
                'required' => false,
                'label' => 'display order',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
            ])
            ->add('menuTitle', TextType::class, [
                'required' => false,
                'label' => 'Menu Title',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
            ])
            ->add('tabTitle', TextType::class, [
                'required' => false,
                'label' => 'Tab title',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
            ])
            //Content tab
            ->add('hasCoverImage', CheckboxType::class, [
                'label' => 'Has Cover?',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
                'label_html' => true,
            ])
            ->add('coverImage', FileType::class, [
                'required' => false,
                'label' => 'Cover',
                'mapped' => false,
                'attr' => ['class' => 'form-control-file'],
                'label_html' => true,
            ])
            ->add('slug', TextType::class, [
                'label' => 'Lien de la page',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
            ])
            ->add('pageTitle', TextType::class, [
                'label' => 'Page title',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
            ])
            ->add('firstContent', TextareaType::class, [
                'label' => 'First Content',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
                'required' => false
            ])
            ->add('secondContent', TextareaType::class, [
                'label' => 'Second Content',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
                'required' => false
            ])
            ->add('thirdContent', TextareaType::class, [
                'label' => 'Third Content',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
                'required' => false
            ])
            ->add('rightContent', TextareaType::class, [
                'label' => 'Right Content',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
                'required' => false
            ])

            ->add('metaTitle', TextType::class, [
                'label' => 'Meta title',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
                'required' => false,

            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Meta description',
                'attr' => ['class' => 'form-control'],
                'label_html' => true,
                'required' => false,

            ])
        
;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'selected_website' => null
        ]);
    }
}
