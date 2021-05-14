<?php

namespace App\Form\User;

use App\Form\UserType;
use App\Form\Model\ChangePassword;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'label.old_password'
            ])
            ->add('newPassword', RepeatedType::class, [
                'label' => 'label.new_password',
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options'  => ['label' => 'label.new_password'],
                'required' => true,
                'second_options' => ['label' => 'label.repeat_password'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
            'translation_domain' => 'users',
        ]);
    }
}