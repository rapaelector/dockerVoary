<?php

namespace App\Controller\ProjectCase;

use App\Entity\Project;

use App\Form\EconomistFormType;
use App\Form\ProjectBusinessChargeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Controller\BaseController;
use App\DataTables\Column\TextColumn;
use App\DataTables\Column\TwigColumn;
use App\DataTables\Column\DateTimeColumn;
use App\DataTables\Adapter\ORMAdapter;
use App\DataTables\DataTable;
use App\DataTables\DataTableFactory;
use App\DataTables\Filter\TextFilter;
use App\DataTables\Filter\ChoiceFilter;
use App\DataTables\Filter\DateRangeFilter;
use App\DataTables\Filter\RangeFilter;
use App\DataTables\Filter\ChoiceRangeFilter;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\DataTableState;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/project/case')]
class ProjectController extends BaseController
{
    /**
     * @isGranted("ROLE_PROJECT_VIEW")
     */
    #[Route('/', name: 'project.case.list')]
    public function index(
        Request $request,
        TranslatorInterface $translator, 
        DataTableFactory $dataTableFactory
    ): Response
    {
        $createOptions = [
            'name' => 'list',
            'translation_domain' => 'client',
        ];
        // create form for business charge and economist
        $project = new Project();
        $businessChargeForm = $this->createForm(ProjectBusinessChargeType::class, $project, array('csrf_protection' => false));
        $economistForm = $this->createForm(EconomistFormType::class, $project, array('csrf_protection' => false));

        $table =  $dataTableFactory->create([], $createOptions)
            ->add('folderNameOnTheServer', TwigColumn::class, [
                'template' => 'project/twig_columns/_document_name.html.twig',
                'label' => $translator->trans('columns.folder_name_on_the_server_tooltip', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.folder_name_on_the_server_raw', [], 'projects'),
                ], true),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('type_text')
                ),
            ])
            /**
             * Change d'chef project en chargé d'affaire (Skype)
             */
            ->add('businessCharge', TwigColumn::class,  [
                'template' => 'project/twig_columns/_business_charge.html.twig',
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('columns.resposible_business_tooltip', [], 'projects'),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.resposible_business_raw', [], 'projects'),
                ], true),
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class,
                    array_merge(
                        $this->filterOptionsProvider->getOptions('business_project_charge'),
                        ['choices' => $this->filterOptionsProvider->getProjectBusinnessCharge()
                    ])
                )
            ])
            ->add('marketType', TextColumn::class, [
                'label' => $translator->trans('columns.market_type_tooltip', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'render' => function ($value, $row) use ($translator) {
                    return $translator->trans($value, [], 'project');
                },
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class,
                    array_merge(
                        $this->filterOptionsProvider->getOptions('project_market_type'),
                        ['choices' => $this->filterOptionsProvider->getProjectMarketType()]
                    )
                ),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.market_type_raw', [], 'projects'),
                ], true),
                'searchable' => true,
            ])
            ->add('codePostal', TextColumn::class, [
                'field' => 'siteAddress.postalCode',
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('columns.code_postal_tooltip', [], 'projects'),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.code_postal_raw', [], 'projects'),
                ], true),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('postal_code')
                ),
            ])
            // LOCALISATION
            ->add('project_site_address', TextColumn::class, [
                'field' => 'siteAddress.city',
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('columns.city', [], 'projects'),
                'meta' => $this->columnMeta([], true),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('project_site_address')
                ),
            ])
            // MONTANT H.T
            ->add('globalAmount', TextColumn::class, [
                'label' => $translator->trans('columns.global_amount', [], 'projects'),
                'render' => $this->numberFormatFactory(0, ',', '.'),
                'className' => 'text-right',
                'meta' => $this->columnMeta([
                    'label_attr' => [
                        'class' => 'text-left text-uppercase',
                    ]
                ], true)
            ])
            // REALISATION
            ->add('completion', TwigColumn::class, [
                'label' => $translator->trans('columns.completion_tooltip', [], 'projects'),
                'template' => 'project_case/twig_columns/_completion.html.twig',
                'className' => 'p-0',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.completion_raw', [], 'projects'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap text-uppercase',
                    ],
                ], true),
            ])
            // DERNIERE RELANCE
            ->add('lastRelaunch', DateTimeColumn::class, [
                'field' => 'lastRelaunch.createdAt',
                'label' => $translator->trans('columns.last_relaunch_tooltip', [], 'projects'),
                'format' => 'd/m/Y',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.last_relaunch_raw', [], 'projects'),
                ], true),
                'filter' => $this->filterBuilder->buildFilter(
                    DateRangeFilter::class,
                    ['type' => 'daterange',]
                ),
            ])
            ->add('planningProject', TextColumn::class, [
                'label' => $translator->trans('columns.planning_project', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' =>  $translator->trans('columns.planning_project_raw', [], 'projects'),
                ], true),
            ])

            // PC DEPOSIT
            ->add('pcDeposit', TwigColumn::class, [
                'label' => $translator->trans('columns.pc_deposit_tooltip', [], 'projects'),
                'className' => 'px-0',
                'template' => 'project_case/twig_columns/_pc_deposite.html.twig',
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class,
                    array_merge(
                        $this->filterOptionsProvider->getOptions('pc_deposit'),
                        [
                            'choices' => [
                                true => $translator->trans('label.yes', [], 'projects'),
                                false => $translator->trans('label.no', [], 'projects'),
                            ],
                            'attr' => [
                                'class' => 'w-100',
                            ],
                        ]
                    )
                ),
                'searchable' => true,
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.pc_deposit_raw', [], 'projects'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap text-uppercase text-left',
                    ]
                ], true),
            ])
            // ARCHITECT
            ->add('architect', TwigColumn::class, [
                'label' => $translator->trans('columns.architect_tooltip', [], 'projects'),
                'className' => 'px-0',
                'template' => 'project_case/twig_columns/_architect.html.twig',
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class,
                    array_merge(
                        $this->filterOptionsProvider->getOptions('architect'),
                        [
                            'choices' => [
                                true => $translator->trans('label.yes', [], 'projects'),
                                false => $translator->trans('label.no', [], 'projects'),
                            ],
                            'attr' => [
                                'class' => 'w-100',
                            ],
                        ]
                    )
                ),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.architect', [], 'projects'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap text-uppercase',
                    ]
                ], true),
            ])
            ->add('norm1090', TextColumn::class, [
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('columns.norm1090', [], 'projects'),
                'meta' => $this->columnMeta([], true),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('norm1090')
                ),
            ])
            ->add('user_email', TextColumn::class, [
                'field' => 'contact.email',
                'label' => $translator->trans('contact.label', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class,
                    array_merge(
                        $this->filterOptionsProvider->getOptions('user_email'),
                        ['choices' => $this->filterOptionsProvider->getProjectInterlocutor()]
                    )
                ),
                'searchable' => true,
            ])
            ->add('id', TextColumn::class, [
                'label' => $translator->trans('label.action', [], 'project'),
                'render' => $this->actionsRenderer('client.list', 'project/_actions.html.twig'),
                'className' => 'text-center',
                'searchable' => false,
                'orderable' => false,
            ])
        ;

        $table->createAdapter(ORMAdapter::class, [
            'entity' => Project::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('project')
                    ->from(Project::class, 'project')
                    ->leftJoin('project.siteAddress', 'siteAddress')
                    ->leftJoin('project.contact', 'contact')
                    ->leftJoin('project.prospect', 'prospect')
                    ->leftJoin('project.businessCharge', 'businessCharge')
                    ->leftJoin('prospect.projectDescription', 'projectDescription')
                    ->leftJoin('project.lastRelaunch', 'lastRelaunch')
                    ->distinct('project')
                ;
            }
        ]);
        
        $table->handleRequest($request);
        if ($table->isCallback()) {
            $adapter = $table->getAdapter();
            $total = 0;
            if ($adapter instanceof ORMAdapter) {
                $adapter->setPostPrepareQuery(function (QueryBuilder $_qb, DataTableState $state) use($total, $table) {
                    $qb = clone $_qb;
                    $aliases = $qb->getAllAliases();

                    if (in_array('project', $aliases)) {
                        $qb->select(sprintf('SUM(%s.globalAmount)', 'project'));
                        $total = $qb->getQuery()->getSingleScalarResult();

                        return ['total' => $total];
                    }
                });
            }

            return $table->getResponse();
        }

        return $this->render('project_case/index.html.twig', [
            'datatable' => $table,
            'meta' => $this->columnMeta([], true),
            'businessChargeForm' => $businessChargeForm->createView(),
            'economistForm' => $economistForm->createView()
        ]);
    }

    /**
     * Update one of the following field in Project entity depend on query->get('field')
     *  - completion
     *  - pcDeposit
     *  - architect
     * - comment
     */
    #[Route('/{id}/update', name: 'project.case.update_project_field', methods: ['POST', 'GET'], options: ['expose' => true])]
    public function updateArchivement(Request $request, EntityManagerInterface $em, Project $project, TranslatorInterface $translator)
    {
        if ($request->isXmlHttpRequest()) {
            $field = $request->query->get('field');
            
            /**
             * Check which of the following field should be updated depend on query->get('field')
             *  - completion
             *  - pcDeposit
             *  - architect
             *  - comment
             * Update project
             * 
             */
            $message = $this->updateSpecificField($project, $request, $field);
            $em->flush();

            return $this->json(['type' => 'success', 'message' => $translator->trans($message, [], 'projects')]);
        }
    }

    /**
     * Update project specific field
     * Field should be one of the following
     *     - ['completion', 'pcDeposit', 'architect', 'comment']
     * 
     * @var Project $project
     * @var Request $request
     * @var String $field
     * 
     * Return custom message depend on the updated field
     * @return String $message
     */
    private function updateSpecificField(Project $project, Request $request, $field)
    {
        $message = '';

        if ($field == 'completion') {
            $completionValue = $request->request->get('completionValue');
            $project->setCompletion($completionValue);
            $project->setVisibleInPlanning(false);
            $message = 'messages.completion_saved';
        } else if ($field == 'pc_deposit') {
            $pcDepositValue = $request->request->get('pcDepositValue');
            try {
                $project->setPcDeposit($pcDepositValue);
                $message = 'messages.pc_deposit_saved';
            } catch (\Exception $e) {
                return $this->json(['type' => 'error', 'message' => $transition->trans('messages.pc_deposit_saving_faild', [], 'projects')]);
            }
        } else if ($field == 'architect') {
            $architectValue = $request->request->get('architectValue');
            try {
                $project->setArchitect($architectValue);
                $message = 'messages.architect_saved';
            } catch (\Exception $e) {
                return $this->json(['type' => 'error', 'message' => $transition->trans('messages.architect_saving_failed', [], 'projects')]);
            }
        } else if ($field == 'comment') {
            $commentValue = $request->request->get('commentValue');
            $project->setComment($commentValue);
            $message = 'messages.comment_saved';
        }

        return $message;
    }
}
