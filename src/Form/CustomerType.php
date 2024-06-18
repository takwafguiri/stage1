<?php

namespace App\Form;

use App\Enum\UserRole;
use Doctrine\ORM\EntityRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Customer;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Prospect;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Customer $object */
        $object = $options['data'];
        $website = $object->getWebsite()->getId();
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
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'john@example.com'],
                'required' => false,
                'label_html' => true,
                'label_attr' => ['class' => "form-label"],
                'label' => 'Email',
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

            ->add('commercial', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($website) {
                     $er = $er->createQueryBuilder('u');
                     $er->leftJoin('u.websites', 'w');
                     $er->leftJoin('w.prospects', 'p');
                     $er->leftJoin('p.website', 'pw');
                    $er->andWhere($er->expr()->like('u.roles', ":commercialRole"))
                        ->setParameter("commercialRole", "%".UserRole::ROLE_COMMERCIAL."%");
                        return $er->andWhere($er->expr()->eq('w.id' , $website))
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
            'data_class' => Customer::class,
            'selected_website' => null
        ]);
    }
}
