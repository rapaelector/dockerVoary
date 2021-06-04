<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Project;
use App\Form\User\ContactType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Constants\Project as Constants;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roadmap', ChoiceType::class, [
                    "label" => 'columns.roadmap',
                    'choices' => [
                        'oui' => true,
                        'non' => false,
                    ],
                    'label_attr' => array(
                        'class' => 'radio-inline'
                    ),
                    "multiple" => false,
                    "expanded" => true
                ])
            ->add('siteCode', TextType::class, [
              'label' => "columns.siteCode"
            ])
            ->add('contactSelect', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.email', 'ASC');
                },
                'placeholder' => 'Nouveau Contact',
                'choice_label' => 'email',
                'required' => false,
                'attr' => [
                    'class' => 'bootstrap-select interlocutor-select',
                    'data-live-search' => true,
                    'data-type' => 'select2'
                ],
                'empty_data' => '',
                'mapped' => false
            ])
            ->add('projectOwner', null, [
                'label' => 'columns.projectOwner'
            ])
            ->add('projectManager', null, [
                'label' => 'columns.projectManager'
            ])
            ->add('marketType', ChoiceType::class, [
                "choices" => Constants::getTypeValues(Constants::TYPE_DE_MARCHE, true),
                'label' => 'columns.marketType',
                "multiple" => false,
                "expanded" => true,
                'label_attr' => array(
                    'class' => 'radio-inline'
                ),
            ])
            ->add('bonhomePercentage', ChoiceType::class, [
                "choices" => Constants::getTypeBonhomme(true),
                'label' => 'columns.is_bonhomme',
                "multiple" => false,
                "expanded" => false,

            ])
            ->add('disaSheetValidation', ChoiceType::class, [
                "choices" => Constants::getTypeValues(Constants::TYPE_DISA_SHEET, true),
                'label' => false,
                "multiple" => true,
                "expanded" => true,
            ])
            ->add('paymentChoice',ChoiceType::class, [
                'label' => 'columns.paymentChoice',
                'choices' => [
                    'oui' => true,
                    'non' => false,
                ],
                "multiple" => false,
                "expanded" => true,
                'label_attr' => array(
                    'class' => 'radio-inline'
                ),
            ])
            ->add('depositeDateEdit', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('paymentPercentage', NumberType::class, [
                'label' => 'columns.paymentPercentage',
                'attr' => [
                    'placeholder' => '%'
                ]
            ])
            ->add('clientCondition', TextareaType::class, [
                'label' => 'columns.clientCondition'
            ])
            ->add('caseType', ChoiceType::class, [
                "choices" => Constants::getTypeValues(Constants::CASE_TYPES, true),
                'label' => false,
                "multiple" => true,
                "expanded" => true,
                'label_attr' => array(
                    'class' => 'checkbox-inline'
                ),
            ])
            ->add('planningProject', TextareaType::class, [
                'label' => false
            ])
            ->add('contact', ContactType::class)
            ->add('billingAddres', AddressType::class)
            ->add('siteAddress', AddressType::class)
            ->add('descriptionOperation', TextareaType::class)
            ->add('soldBy', null, [
                'label' => false
            ])
            ->add('quoteWriter', null, [
                'label' => false
            ])
            ->add('norm1090', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                ],
                'label_attr' => array(
                    'class' => 'radio-inline'
                ),
                "multiple" => false,
                "expanded" => true
            ])
            ->add('quoteValidatedMDE', null, [
                'label' => 'columns.quoteValidatedMDE'
            ])
            ->add('quoteValidatedMDEDate', DateType::class, [
                'label' => 'columns.quoteValidatedMDEDate',
                'widget' => 'single_text',
            ])
            ->add('globalAmount', null, [
                'label' => 'columns.globalAmount'
            ])
            ->add('amountSubcontractedWork', null, [
                'label' => 'columns.amountSubcontractedWork'
            ])
            ->add('amountBBISpecificWork', null, [
                'label' => 'columns.amountBBISpecificWork'
            ])
            ->add('recordAssistant', null,[
                'label' => false
            ])
            ->add('ocbsDriver', null,[
                'label' => false
            ])
            ->add('tceDriver', null,[
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'translation_domain' => 'project',
        ]);
    }
}
