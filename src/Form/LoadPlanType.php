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

class LoadPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('natureOfTheCosting', TextType::class)
            // ->add('weekNumber', TextType::class)
            ->add('start', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class
            ])
            ->add('estimatedStudyTime', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoadPlan::class,
        ]);
    }
}
