<?php

namespace App\Form;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\WebsiteCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;
use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\QueryBuilder;



class WebsiteCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var WebsiteCategory $object */
        $object = $options['data'];
        $selectedWebsite = $options['selected_website'];
        $builder
            ->add('tag', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Tag'
            ])
            ->add('label', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Display label'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Description',
            ])
            ->add('picture', FileType::class, [
                'label' => 'Picture',
                'required' => false,
                'mapped' => false, // Important, car nous gérons le téléchargement manuellement
                'constraints' => [
                    new Image([
                        'maxSize' => '5000k'

                    ])
                ]
            ])
            ->add('parent', EntityType::class, [
                'class' => WebsiteCategory::class,
                'attr' => ['class' => 'form-control'],
                'choice_label' => function (WebsiteCategory $category): string {
                    return $category->getTag() . ' | ' . $category->getLabel();
                },
                'query_builder' => function (EntityRepository $er) use ($selectedWebsite): QueryBuilder {
                    $qb = $er->createQueryBuilder('parent');
                    $qb->andWhere($qb->expr()->isNull('parent.parent'))
                        ->andWhere($qb->expr()->eq('parent.website', ':websiteId'))
                        ->setParameter('websiteId', $selectedWebsite);
                    return $qb->orderBy('parent.label', 'ASC');
                },
                'placeholder' => 'No Parent', 
                'required' => false, 
                'multiple' => false,
                'expanded' => false,
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'label' => 'Parent Category',
            ])
            ->add('isEnabled', CheckboxType::class, [
                'label' => 'Category enabled',
                'required' => false,
                'label_html'  => true,
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ])->add('isServiceParent', CheckboxType::class, [
                'label' => 'Is a parent service',
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
            'data_class' => WebsiteCategory::class,
            'selected_website' => null 
        ]);
    }
}
