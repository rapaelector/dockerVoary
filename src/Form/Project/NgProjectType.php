<?php

namespace App\Form\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\ProjectEvent;
use App\Entity\Constants\Project as Constants;

use App\Form\ProjectEditType;
use App\Form\ProjectEventType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class NgProjectType extends ProjectEditType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('globalAmount')
            ->remove('amountSubcontractedWork')
            ->remove('amountBBISpecificWork')
            ->remove('encryptiontype')
            ->remove('roadmap')
            ->remove('siteCode')
            ->add('scope', ChoiceType::class, [
                'label' => false,
                'choices' => Constants::SECOND_MARKET_TYPES,
            ])
            ->add('contact', EntityType::class, [
                'class' => User::class,
            ])
            ->add('name', TextType::class)
            ->add('events', CollectionType::class, [
                'entry_type' => ProjectEventType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }
}