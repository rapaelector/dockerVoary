<?php

namespace App\Form;

use App\Entity\LoadPlan;
use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Constraints;

class LoadPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('natureOfTheCosting', TextType::class)
            // ->add('weekNumber', TextType::class)
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new Constraints\NotNull(),
                ]
            ])
            ->add('deadline', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
            ])
            ->add('realizationQuotationDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class
            ])
            ->add('estimatedStudyTime', ChoiceType::class, [
                'choices' => LoadPlan::STUDY_TIME,
            ])
            ->add('effectiveStudyTime', ChoiceType::class, [
                'choices' => LoadPlan::STUDY_TIME,
            ])
            ->add('type', ChoiceTYpe::class, [
                'choices' => LoadPlan::TYPES,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoadPlan::class,
        ]);
    }
}
