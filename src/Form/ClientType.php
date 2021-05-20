<?php

namespace App\Form;

use App\Entity\Client;
use App\Form\UserType;
use App\Form\AddressType;
use App\Form\User\ContactsType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'required' => false,
            ])
            ->add('shortName', TextType::class, [
                'label' => 'label.short_name',
                'required' => false,
            ])
            ->add('clientNumber', TextType::class, [
                'label' => 'label.client_number',
                'required' => false,
            ])
            ->add('activity', ChoiceType::class, [
                'label' => 'label.activity',
                'choices' => Client::getActivityChoices(true),
                'required' => false,
            ])
            ->add('tvaRate', TextType::class, [
                'label' => 'label.tva_rate',
                'required' => false,
                'attr' => [
                    'data-choices' => json_encode(Client::getTvaChoices()),
                    'data-type' => 'select',
                ]
            ])
            ->add('siret', TextType::class, [
                'label' => 'label.siret',
                'required' => false,
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'label.payment_method',
                'required' => false,
                'choices' => Client::getPaymentTypeChoices(true),
            ])
            ->add('payment', ChoiceType::class, [
                'label' => 'label.payment',
                'required' => false,
                'choices' => Client::getPaymentPeriodChoices(true),
            ])
            ->add('intraCommunityTva', TextType::class, [
                'label' => 'label.intra_community_tva',
                'required' => false,
            ])
            ->add('billingAddress', AddressType::class, [
                'label' => 'label.billing_address',
                'required' => false,
            ])
            ->add('contacts', CollectionType::class, [
                'label' => false,
                'entry_type' => ContactsType::class,
                'block_name' => 'client_contacts',
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'translation_domain' => 'client',
        ]);
    }
}