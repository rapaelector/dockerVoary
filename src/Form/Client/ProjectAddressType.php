<?php

namespace App\Form\Client;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class ProjectAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('line1', TextType::class, [
                'label' => 'project_description.line1',
                'attr' => [
                    'class' => 'project-address-fields',
                ],
                'label_attr' => [
                    'class' => 'text-nowrap',
                ],
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'project_description.postal_code',
                'attr' => [
                    'class' => 'project-address-fields',
                ],
                'label_attr' => [
                    'class' => 'text-nowrap',
                ],
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'project_description.city',
                'attr' => [
                    'class' => 'project-address-fields',
                ],
                'label_attr' => [
                    'class' => 'text-nowrap',
                ],
                'required' => false,
            ])
            ->add('country', CountryType::class, [
                'label' => 'project_description.country',
                'required' => false,
                'attr' => [
                    'class' => 'bootstrap-select form-control project-address-fields',
                    'data-live-search' => true,
                    'data-type' => 'select2',
                ],
                'preferred_choices' => function ($val, $key) {
                    return $val == 'FR';
                },
                'data'=> 'FR',
                'label_attr' => [
                    'class' => 'text-nowrap',
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'translation_domain' => 'client',
        ]);
    }
}
