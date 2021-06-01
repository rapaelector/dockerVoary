<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('marketType')
            ->add('bonhomePercentage')
            ->add('disaSheetValidation')
            ->add('paymentChoice',ChoiceType::class, [
                'label' => 'label.payment_method',
                'required' => false,
                'choices' => Project::getPaymentTypeChoices(true),
            ])
            ->add('interlocuteurSelect', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.email', 'ASC');
                },
                'choice_label' => 'email',
                'required' => false,
                'attr' => [
                    'class' => 'bootstrap-select',
                    'data-live-search' => true,
                    'data-type' => 'select2'
                ],
                'mapped' => false
            ])
            ->add('interlocuteur', UserType::class)
            ->add('billingAddres', AddressType::class)
            ->add('siteAddress', AddressType::class)
            ->add('descriptionOperation', TextareaType::class)
            ->add('soldBy')
            ->add('quoteWriter')
            ->add('norm1090')
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
