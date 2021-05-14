<?php

namespace App\Form\User;

use App\Form\UserType;
use App\Form\Model\ChangePassword;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'label.old_password'
            ])
            ->add('newPassword', RepeatedType::class, [
                'label' => 'label.new_password',
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans('label.invalid_message', [], 'users'),
                'first_options'  => ['label' => 'label.new_password'],
                'second_options' => ['label' => 'label.repeat_password'],
                'required' => true,
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