<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Project;
use App\Form\User\ContactType;
use Doctrine\ORM\EntityRepository;
use App\Utils\Resolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Constants\Project as Constants;

class ProjectType extends AbstractType
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'columns.project_name',
                'required' => false,
            ])
            ->add('prospect', null, [
                'label' => 'columns.prospect'
            ])
            ->add('contactSelect', EntityType::class, [
                'label' => 'columns.contact',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.lastName, u.firstName', 'ASC');
                },
                'placeholder' => 'Nouveau Contact',
                // 'choice_label' => 'lastName',
                'required' => false,
                'attr' => [
                    'class' => 'bootstrap-select interlocutor-select',
                    'data-live-search' => true,
                    'data-type' => 'select2'
                ],
                'empty_data' => '',
            ])
            ->add('businessCharge', null, [
                'label'=>"columns.businessCharge"
            ])
            ->add('economist', null, [
                'label' => "columns.economist"
            ])
            ->add('descriptionOperation', TextareaType::class)
            ->add('contact', ContactType::class)
            ->add('caseType', ChoiceType::class, [
                "choices" => Constants::getTypeValues(Constants::CASE_TYPES, true),
                'label' => "columns.caseType",
                "multiple" => true,
                "expanded" => true,
                'label_attr' => array(
                    'class' => 'checkbox-custom checkbox-inline'
                ),
            ])
            ->add('priorizationOfFile', ChoiceType::class, [
                'label'=> "columns.priorizationOfFile",
                "choices" => Constants::getTypeValues(Constants::PRIORIZATION_FILE_TYPE, true),
                "multiple" => false,
                "expanded" => true,
                'label_attr' => [
                    'class' => 'radio-custom radio-inline'
                ],
            ])
            ->add('planningProject', TextareaType::class, [
                'label' => 'columns.planningProject',
                'required' => false,
            ])
            ->add('answerForThe', null, [
                'label'=>"columns.answerForThe",
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onPreSetData']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'translation_domain' => 'project',
        ]);
    }

    public function onPreSetData(FormEvent $event)
    {
        $contact = null;
        $request = $this->requestStack->getCurrentRequest();
        $contactSelect = Resolver::resolve([$request->request->all(), $this->getBlockPrefix(), 'contactSelect'], null);

        if ($contactSelect) {
            $contact = $this->em->getRepository(User::class)->find($contactSelect);
        }

        if ($contact) {
            $form = $event->getForm();
            $formContact = $event->getData()->getContact();
            $contact->setLastName($formContact->getLastName());
            $contact->setFirstName($formContact->getFirstName());
            $contact->setEmail($formContact->getEmail());
            $contact->setPhone($formContact->getPhone());
            $contact->setJob($formContact->getJob());
            $contact->setFax($formContact->getFax());

            $event->getData()->setContact($contact);
        }
    }
}
