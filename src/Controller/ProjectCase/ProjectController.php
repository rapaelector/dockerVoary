<?php

namespace App\Controller\ProjectCase;

use App\Entity\Project;
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

        $table =  $dataTableFactory->create([], $createOptions)
            ->add('folderNameOnTheServer', TextColumn::class, [
                'label' => $translator->trans('columns.folder_name_on_the_server', [], 'projects'),
                'meta' => $this->columnMeta([], true),
            ])
            ->add('businessCharge', TextColumn::class,  [
                'field' => 'businessCharge.lastName',
                'label' => $translator->trans('columns.business_charge', [], 'projects'),
                'meta' => $this->columnMeta([], true),
            ])
            ->add('marketType', TextColumn::class, [
                'label' => $translator->trans('columns.market_type', [], 'projects'),
                'className' => 'dynamic-nowrap',
                'render' => function ($value, $row) use ($translator) {
                    return $translator->trans($value, [], 'project');
                },
                'meta' => $this->columnMeta([], true),
            ])
            ->add('project_description_area', TextColumn::class, [
                'field' => 'projectDescription.area',
                'label' => $translator->trans('columns.project_description_area', [], 'projects'),
                'meta' => $this->columnMeta([], true)
            ])
            ->add('codePostal', TextColumn::class, [
                'field' => 'siteAddress.postalCode',
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('columns.code_postal', [], 'projects'),
                'meta' => $this->columnMeta([], true),
            ])
            // LOCALISATION
            ->add('project_site_address', TextColumn::class, [
                'field' => 'siteAddress.city',
                'className' => 'dynamic-nowrap',
                'label' => $translator->trans('columns.city', [], 'projects'),
                'meta' => $this->columnMeta([], true)
            ])
            ->add('globalAmount', TextColumn::class, [
                'label' => $translator->trans('columns.global_amount', [], 'projects'),
                'render' => $this->numberFormatFactory(0, ',', ' '),
                'className' => 'text-right',
                'meta' => $this->columnMeta([], true)
            ])
            // REALISATION
            ->add('archivementPourcentage', TwigColumn::class, [
                'label' => $translator->trans('columns.production_rate', [], 'projects'),
                'template' => 'project_case/twig_columns/_archivement_pourcentage.html.twig',
                'className' => 'p-0',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.production_rate', [], 'projects'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap text-uppercase',
                    ],
                ], true),
            ])
            ->add('lastRelaunch', DateTimeColumn::class, [
                'field' => 'lastRelaunch.createdAt',
                'label' => $translator->trans('columns.last_relaunch', [], 'projects'),
                'format' => 'd/m/Y',
                'meta' => $this->columnMeta([], true),
            ])
            ->add('planningProject', TextColumn::class, [
                'label' => $translator->trans('columns.planning_project', [], 'projects'),
                'meta' => $this->columnMeta([], true)
            ])
            ->add('contact_name', TextColumn::class, [
                'field' => 'contact.lastName',
                'label' => $translator->trans('columns.contact_name', [], 'projects'),
                'meta' => $this->columnMeta([], true)
            ])
            ->add('comment', TextColumn::class, [
                'label' => $translator->trans('columns.comment', [], 'projects'),
                'meta' => $this->columnMeta([], true)
            ])
            // ->add('id', TextColumn::class, [
            //     'label' => $translator->trans('action.action'),
            //     'render' => $this->actionsRenderer('client.list', 'current_case/_actions.html.twig'),
            //     'className' => 'text-center',
            //     'searchable' => false,
            //     'orderable' => false,
            //     'meta' => $this->columnMeta([], true)
            // ])
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
            'meta' => $this->columnMeta([], true)
        ]);
    }
}
