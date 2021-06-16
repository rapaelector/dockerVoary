<?php

namespace App\Form\Client;

use App\Entity\Client\ProjectDescription;
use App\Entity\Constants\Project as Constants;
use App\Entity\Constants\Project\MarketType;
use App\Form\Client\ProjectAddressType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProjectDescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectDescription', TextType::class, [
                'label' => 'columns.project_description',
            ])
            ->add('area', TextType::class, [
                'label' => 'columns.area',
            ])
            ->add('marketType', ChoiceType::class, [
                'label' => 'columns.marketType',
                'choices' => Constants::getTypeValues(Constants::FOLDER_PROSPECTION_MARKET_TYPE_CHOICE, true),
                'label_attr' => [
                    'class' => '',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('department', TextType::class, [
                'label' => 'columns.department',
            ])
            ->add('address', ProjectAddressType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectDescription::class,
            'translation_domain' => 'project',
        ]);
    }
}
