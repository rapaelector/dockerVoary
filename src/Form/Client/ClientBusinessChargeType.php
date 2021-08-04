<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientBusinessChargeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('businessCharge', EntityType::class, [
                'label' => 'columns.businessCharge',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.email', 'ASC');
                },
                'placeholder' => '',
                'choice_label' => 'fullNameWithEmail',
                'required' => false,
                'attr' => [
                    'class' => 'bootstrap-select interlocutor-select',
                    'data-live-search' => true,
                    'data-type' => 'select2'
                ],
                'empty_data' => ''
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'translation_domain' => 'project',
        ]);
    }
}
