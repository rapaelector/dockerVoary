<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'label.lastName',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'label.firstName',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'label.email',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('job', TextType::class, [
                'label' => 'label.job',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'users',
        ]);
    }
}
