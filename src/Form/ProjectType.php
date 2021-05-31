<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteCode')
            ->add('roadmap')
            ->add('projectOwner')
            ->add('projectManager')
            ->add('billingAddres')
            ->add('contactName')
            ->add('email')
            ->add('phone')
            ->add('siteAddress')
            ->add('descriptionOperation')
            ->add('soldBy')
            ->add('quoteWriter')
            ->add('norm1090')
            ->add('marketType')
            ->add('bonhomePercentage')
            ->add('disaSheetValidation')
            ->add('paymentChoice')
            ->add('depositeDateEdit', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('clientCondition')
            ->add('quoteValidatedMDE')
            ->add('quoteValidatedMDEDate')
            ->add('globalAmount')
            ->add('amountSubcontractedWork')
            ->add('amountBBISpecificWork')
            ->add('caseType')
            ->add('planningProject')
            ->add('recordAssistant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
