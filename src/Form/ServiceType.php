<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Service;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\WebsiteCategory;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedWebsite = $options['selected_website'];
        $builder
            ->add('tag', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Tag'
            ])
            ->add('category', EntityType::class, [
                'class' => WebsiteCategory::class,
                'attr' => ['class' => 'form-control'],
                'choice_label' => function (WebsiteCategory $category): string {
                    return $category->getTag() . ' | ' . $category->getLabel();
                },
                'query_builder' => function (EntityRepository $er) use ($selectedWebsite): QueryBuilder {
                    $qb = $er->createQueryBuilder('category');
                    $qb->andWhere($qb->expr()->eq('category.website', ':websiteId'))
                        ->setParameter('websiteId', $selectedWebsite);
                    return  $qb->orderBy('category.label', 'ASC');
                },
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Category'
            ])
            ->add('label', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Display name'
            ])
            ->add('dureeDeService', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Durée De Service'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Description',
            ])
            ->add('unitPrice', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'scale' => 2,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Price',

            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'required' => false,
                'mapped' => false, // Important, car nous gérons le téléchargement manuellement
                'constraints' => [
                    new Image([
                        'maxSize' => '5000k'

                    ])
                ]
            ])
            ->add('useInDevisForm', CheckboxType::class, [
                'label' => 'Use in devis form',
                'required' => false,
                'label_html'  => true,
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ])
            ->add('isEnabled', CheckboxType::class, [
                'label' => 'Service enabled',
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
            'data_class' => Service::class,
            'selected_website' => null
        ]);
    }
}
