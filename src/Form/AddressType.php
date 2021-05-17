<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
                'required' => false,
            ])
            ->add('fax', TextType::class, [
                'label' => 'label.fax',
                'required' => false,
            ])
            ->add('line1', TextType::class, [
                'label' => 'label.line1',
                'required' => false,
            ])
            ->add('line2', TextType::class, [
                'label' => 'label.line2',
                'required' => false,
            ])
            ->add('line3', TextType::class, [
                'label' => 'label.line3',
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'label.postal_code',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'label.city',
                'required' => false,
            ])
            ->add('country', CountryType::class, [
                'label' => 'label.country',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'translation_domain' => 'address',
        ]);
    }
}
