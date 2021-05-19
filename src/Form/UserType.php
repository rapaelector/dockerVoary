<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
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
            ->add('password', PasswordType::class, [
                'label' => 'label.password',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fax', TextType::class, [
                'label' => 'label.fax',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            ->add('job', TextType::class, [
                'label' => 'label.job',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            ->add('canLogin', CheckboxType::class, [
                'label' => 'label.can_login',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom',
                ],
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
