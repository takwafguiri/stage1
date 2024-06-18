<?php

namespace App\Form;

use App\Enum\UserRole;
use App\Services\WebsiteServices;
use Doctrine\ORM\EntityRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Prospect;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Service;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ProspectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Prospect $object */
        $object = $options['data'];
        /** @var Website $website */
        $website = $object->getWebsite();
        $categories = $website->getWebsiteCategories();
        $builder
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'First Name'
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
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '+216 55******'],
                'required' => false,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Description',
                'constraints' => []
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'john@example.com'],
                'required' => false,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Email',
                'constraints' => []
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('w')
                        ->orderBy('w.label', 'ASC');
                },
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => false,
                'required' => true,
                'label_html' => true,
                'label' => 'Services',
                'attr' => [
                    'class' => 'select2 form-control',
                    'placeholder' => 'select websites'
                ]])
            ->add('category', ChoiceType::class, [
                'choices'=> $categories,
                'choice_label' => 'label',
                'attr' => ['class' => 'form-control', 'placeholder' => 'john@example.com'],
                'required' => false,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Category',
                'label_html' => true,
                'constraints' => []
            ])
            ->add('website', EntityType::class, [
                'class' => Website::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('w')
                        ->orderBy('w.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'label_html' => true,
                'label' => 'Website',
                'attr' => [
                    'class' => 'select2 form-control',
                    'placeholder' => 'select website'
                ]])
            ->add('birthday', BirthdayType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Birth date',
                'constraints' => []
            ])

            ->add('country', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Pays'
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Ville'
            ])

            ->add('file', FileType::class, [
                'required' => false,
                'label' => 'Dossier médical (images de la partie en question sans montrer votre visage ou pdf)',
                'mapped' => false,
                'attr' => ['class' => 'form-control-file'],
                'label_html' => true,
            ])
            ->add('host', ChoiceType::class, [
                'label' => 'Choisissez votre hebergement ',
                'label_html' => true,
                'label_attr' => ['class' => 'form-label'],
                'choices' => [
                    'Sans hebergement' =>  'Sans hebergement',
                    'Appartement' => 'Appartement',
                    'Hotel' => 'Hotel',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('appointment', DateType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => "Appointment date",
                'constraints' => []
            ])
            ->add('commercial', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($website) {
                     $er = $er->createQueryBuilder('u');
                     $er->leftJoin('u.websites', 'w');
                     $er->leftJoin('w.prospects', 'p');
                     $er->leftJoin('p.website', 'pw');
                    $er->andWhere($er->expr()->like('u.roles', ":commercialRole"))
                        ->setParameter("commercialRole", "%".UserRole::ROLE_COMMERCIAL."%");
                        return $er->andWhere($er->expr()->eq('w.id' , $website->getId()))
                        ->orderBy('u.username', 'ASC');
                },
                'choice_label' => 'username',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'label_html' => true,
                'label' => 'Commercial',
                'attr' => [
                    'class' => 'select2 form-control',
                    'placeholder' => 'select commercial'
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prospect::class,
            'selected_website' => null
        ]);
    }
}
