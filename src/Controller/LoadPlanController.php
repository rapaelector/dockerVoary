<?php

namespace App\Controller;

use App\Entity\LoadPlan;
use App\Entity\Project;
use App\Entity\User;
use App\Controller\BaseController;
use App\Form\LoadPlanType;
use App\DataTables\Column\TextColumn;
use App\DataTables\Column\TwigColumn;
use App\DataTables\Column\DateTimeColumn;
use App\DataTables\Filter\DateRangeFilter;
use App\DataTables\Adapter\ORMAdapter;
use App\DataTables\DataTable;
use App\DataTables\DataTableFactory;
use App\Service\Form\FormService;
use App\DataTables\Filter\ChoiceFilter;
use App\DataTables\Filter\ChoiceRangeFilter;
use App\Service\User\UserService;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @IsGranted("ROLE_LOAD_PLAN_VIEW")
 */
#[Route('/load/plan')]
class LoadPlanController extends BaseController
{
    /**
     * @IsGranted("ROLE_LOAD_PLAN_VIEW")
     */
    #[Route('/', name: 'load_plan.list', options: ['expose' => true])]
    public function index(
        Request $request,
        TranslatorInterface $translator, 
        DataTableFactory $dataTableFactory
    ): Response
    {
        $createOptions = [
            'name' => 'list',
            'translation_domain' => 'projects',
        ];

        $table =  $dataTableFactory->create([], $createOptions)
            // Chargé d'affaire
            ->add('businessCharge', TextColumn::class, [
                'field' => 'businessCharge.firstName',
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('load_plan.label.business_charge', [], 'projects'),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.business_charge_abbr', [], 'projects'),
                    'label_attr' => [
                        'style' => 'text-transform: uppercase',
                    ],
                ], false),
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class, 
                    array_merge(
                        $this->filterOptionsProvider->getOptions('business_charge'),
                        ['choices' => $this->filterOptionsProvider->getLoadPlanBusinessCharge()]
                    )
                ),
            ])
            /**
             * Economist
             * Editable field
             */
            ->add('economist', TwigColumn::class, [
                'field' => 'economist.firstName',
                'template' => 'load_plan/twig_columns/_economist.html.twig',
                'className' => 'dynamic-nowrap editable-field',
                'label' => $translator->trans('columns.economist', [], 'project'),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.economist', [], 'project'),
                    'label_attr' => [
                        'style' => 'text-transform: uppercase',
                    ],
                ], false),
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class,
                    array_merge(
                        $this->filterOptionsProvider->getOptions('business_charge'),
                        ['choices' => $this->filterOptionsProvider->getEconomist()]
                    )
                )
            ])
            // Nom projet
            ->add('name', TextColumn::class, [
                'field' => 'project.name',
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('load_plan.label.project_name', [], 'projects'),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.project_name', [], 'projects'),
                    'label_attr' => [
                        'style' => 'text-transform: uppercase',
                    ],
                ], false),
                'searchable' => true,
            ])
            // Surface
            ->add('area', TextColumn::class, [
                'field' => 'projectDescription.area',
                'label' => $translator->trans('load_plan.label.area', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.area', [], 'projects'),
                ], true),
            ])
            // Activité
            ->add('activity', TextColumn::class, [
                'field' => 'project.marketType',
                'label' => $translator->trans('load_plan.label.activity', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'render' => function ($value, $row) use ($translator) {
                    return $value ? $translator->trans($value, [], 'project') : '';
                },
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.activity', [], 'projects'),
                ], true),
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class, 
                    array_merge(
                        $this->filterOptionsProvider->getOptions('load_plan_activity'),
                        ['choices' => $this->filterOptionsProvider->getPlanActivities()]
                    )
                ),
            ])
            // CP
            ->add('siteAddressPostalCode', TextColumn::class, [
                'field' => 'siteAddress.postalCode',
                'label' => $translator->trans('label.postal_code_abbr', [], 'address'),            
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('label.postal_code_abbr', [], 'address'),
                ], true),
            ])
            // Localisation
            ->add('location', TextColumn::class, [
                'field' => 'siteAddress.line1',
                'label' => $translator->trans('load_plan.label.location', [], 'projects'),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.location', [], 'projects'),
                ], true),
            ])
            // Nature du chiffrage
            ->add('natureOfTheCosting', TextColumn::class, [
                'label' => $translator->trans('load_plan.label.nature_of_the_costing', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'render' => function ($value, $row) use ($translator) {
                    return $translator->trans('load_plan.task_type.' .$value, [], 'projects');
                },
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.nature_of_the_costing', [], 'projects'),
                ], true),
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class, 
                    array_merge(
                        $this->filterOptionsProvider->getOptions('nature_of_the_costing'),
                        ['choices' => $this->filterOptionsProvider->getNatureOfTheCosting()]
                    )
                ),
            ])
            // Temps d'etude estime
            ->add('estimatedStudyTime', TextColumn::class, [
                'label' => $translator->trans('load_plan.label.estimated_study_time', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'render' => function ($value, $row) use ($translator) {
                    return $value ? $translator->trans('load_plan.study_time.' .$value, [], 'projects') : '';
                },
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.estimated_study_time', [], 'projects'),
                ], true),
                'searchable' => true,
            ])
            /**
             * Date butoire
             * Editable field
             */
            ->add('deadline', TwigColumn::class, [
                'label' => $translator->trans('load_plan.label.deadline', [], 'projects'),
                'template' => 'load_plan/twig_columns/_deadline.html.twig',
                'className' => 'editable-field',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.deadline', [], 'projects'),
                ], true),
            ])
            /* 
             * N° semaine pour remise de l'etude
             * Editable field
             */
            ->add('start', TwigColumn::class, [
                'label' => $translator->trans('load_plan.label.week_number_for_submission_of_the_study', [], 'projects'),
                'template' => 'load_plan/twig_columns/_week_number.html.twig',
                'className' => 'text-center p-0 editable-field',
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceRangeFilter::class,
                    array_merge(
                        $this->filterOptionsProvider->getOptions('load_plan_start'),
                        ['choices' => $this->filterOptionsProvider->getLoadPlanStart()],
                    ),
                ),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.week_number_for_submission_of_the_study_abbr', [], 'projects'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap',
                        'style' => 'text-transform: uppercase',
                    ],
                ], true),
            ])
            // Commentaires
            ->add('comment', TextColumn::class, [
                'field' => 'project.planningProject',
                'label' => $translator->trans('load_plan.label.comment', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.comment', [], 'projects'),
                ], true),
            ])
            /**
             * Date de devis
             * Date de realisation devis
             * Editable field
             */
            ->add('realizationQuotationDate', TwigColumn::class, [
                'label' => $translator->trans('load_plan.label.realization_quotation_date', [], 'projects'),
                'template' => 'load_plan/twig_columns/_realization_quotation_date.html.twig',
                'className' => 'editable-field',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.realization_quotation_date_abbr', [], 'projects'),
                ], true),
                'searchable' => true,
                'orderable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    DateRangeFilter::class, 
                    [
                        'type' => 'daterange',
                    ]
                )
            ])
            // Temps d'etude effectif
            ->add('effectiveStudyTime', TextColumn::class, [
                'label' => $translator->trans('load_plan.label.effective_study_time', [], 'projects'),
                'render' => function ($value, $row) use ($translator) {
                    return $value ? $translator->trans('load_plan.study_time.' .$value, [], 'projects') : '';
                },
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('load_plan.label.effective_study_time_abbr', [], 'projects'),
                ], true),
                'searchable' => true,
            ])
            ->add('id', TextColumn::class, [
                'label' => $translator->trans('label.action'),
                'render' => $this->actionsRenderer('load_plan.list', 'load_plan/_actions.html.twig'),
                'className' => 'text-center',
                'searchable' => false,
                'orderable' => false,
            ])
        ;

        $table->createAdapter(ORMAdapter::class, [
            'entity' => LoadPlan::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('loadPlan')
                    ->from(LoadPlan::class, 'loadPlan')
                    ->leftJoin('loadPlan.project', 'project')
                    ->leftJoin('project.siteAddress', 'siteAddress')
                    ->leftJoin('project.prospect', 'prospect')
                    ->leftJoin('prospect.projectDescription', 'projectDescription')
                    ->leftJoin('project.businessCharge', 'businessCharge')
                    ->leftJoin('project.economist', 'economist')
                    ->distinct('loadPlan')
                ;
            }  
        ]);
        
        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('load_plan/index.html.twig', [
            'datatable' => $table,
        ]);
    }

    /**
     * @IsGranted("ROLE_LOAD_PLAN_ADD")
     */
    #[Route('/new', name: 'load_plan.new', options: ['expose' => true])]
    public function new(
        Request $request, 
        EntityManagerInterface $em, 
        SerializerInterface $serializer, 
        TranslatorInterface $translator,
        FormService $formService
    )
    {
        $loadPlan = new LoadPlan();
        $form = $this->createForm(LoadPlanType::class, $loadPlan, [
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isSubmitted() && $form->isValid()) {
            $loadPlan->setEnd((clone $loadPlan->getStart())->modify('Next sunday'));
            $em->persist($loadPlan);
            $em->flush();

            $normalizedLoadPlan = $serializer->normalize($loadPlan, 'json', ['groups' => 'loadPlan:list']);
            return $this->json([
                'message' => $translator->trans('load_plan.messages.saving_successfull', [], 'projects'),
                'data' => $normalizedLoadPlan,
            ]);
        }

        return $this->json([
            'message' => $translator->trans('load_plan.messages.saving_errors', [], 'projects'),
            'errors' => $formService->getErrorsFromForm($form)
        ], 400);
    }

    /**
     * Edition of end date when update start date (semaine pur remise de l'étude)
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), loadPlan)")
     */
    #[Route('/{id}/edit', name: 'load_plan.edit', options: ['expose' => true], requirements: ["id" => "\d+"])]
    public function edit(Request $request, LoadPlan $loadPlan, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        if ($request->getMethod() == 'POST') {
            $form = $this->createForm(LoadPlanType::class, $loadPlan, [
                'csrf_protection' => false,
                'allow_extra_fields' => true,
            ]);

            $form->submit(json_decode($request->getContent(), true));

            if ($form->isSubmitted() && $form->isValid()) {
                $loadPlan->setEnd((clone $loadPlan->getStart())->modify('Next sunday'));
                $em->flush();
    
                return $this->json(['message' => $translator->trans('load_plan.messages.edit_successfull', [], 'projects')]);
            }

            return $this->json(['message' => $translator->trans('load_plan.messages.edit_failed', [], 'projects')], 400);
        }

        return $this->json(['message' => $translator->trans('load_plan.messages.edit_failed', [], 'projects')], 400);
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::DELETE'), loadPlan)")
     */
    #[Route('/{id}/delete', name: 'load_plan.delete', methods: ['POST', 'DELETE'], options: ['expose' => true])]
    public function delete(Request $request, LoadPlan $loadPlan, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        if ($this->isCsrfTokenValid('delete'.$loadPlan->getId(), $request->request->get('_token'))) {
            $em->remove($loadPlan);
            $em->flush();

            return $this->json([
                'message' => $translator->trans('load_plan.messages.delete_successfull', [], 'projects')
            ]);
        }

        return $this->redirectToRoute('load_plan.list');
    }
    
    #[Route('/projects', name: 'load_plan.projects', options: ['expose' => true])]
    public function projects(Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $projects = $em->getRepository(Project::class)->findAll();
        $normalizedProjects = $serializer->normalize($projects, 'json', ['groups' => 'loadPlan:create']);
        
        return $this->json($normalizedProjects);
    }

    #[Route('/config', name: 'load_plan.config', options: ['expose' => true])]
    public function config(Request $request, TranslatorInterface $translator)
    {
        $taskTypesTranslated = [];
        foreach (LoadPlan::TASK_TYPES as $task) {
            $taskTypesTranslated[] = ['label' => $translator->trans('load_plan.task_type.' .$task, [], 'projects'), 'value' => $task];
        }

        $studyTime = [];
        foreach (LoadPlan::STUDY_TIME as $time) {
            $studyTime[] = ['label' => $translator->trans('load_plan.study_time.' .$time, [], 'projects'), 'value' => $time];
        }
        
        $types = [];
        foreach (LoadPlan::TYPES as $type) {
            $types[] =  ['label' => $translator->trans('load_plan.type.' .$type, [], 'projects'), 'value' => $type];
        }

        return $this->json([
            'taskTypes' => $taskTypesTranslated,
            'studyTime' => $studyTime,
            'types' => $types,
        ]);
    }

    #[Route('/{id}', name: 'load_plan.get_load_plan', options: ['expose' => true], requirements: ["id" => "\d+"])]
    public function loadPlan(Request $request, LoadPlan $loadPlan, SerializerInterface $serializer)
    {
        return $this->json($serializer->normalize($loadPlan, 'json', ['groups' => 'loadPlan:list']));
    }

    #[Route('/economists', name: 'load_plan.economists', options: ['expose' => true])]
    public function economists(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        UserService $userService
    )
    {
        $query = $request->query->get('q');
        $economists = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.firstName LIKE :firstName OR u.lastName LIKE :lastName')
            ->setParameters([
                'firstName' => '%' . $query . '%',
                'lastName' => '%' .$query. '%',
            ])
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ; 
        $normalizedEconomists = $serializer->normalize($economists, 'json', ['groups' => 'loadPlan:economist']);
        $res = [];

        foreach ($economists as $key => $economist) {
            $normalizedEconomists[$key]['avatar'] = $userService->getUserAvatar($economist);
            $res[] = $normalizedEconomists[$key];
        }

        return $this->json($res, 200);
    }
}
