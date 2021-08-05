<?php

namespace App\Controller;

use App\Entity\LoadPlan;
use App\Entity\Project;
use App\Controller\BaseController;
use App\DataTables\Column\TextColumn;
use App\DataTables\Column\TwigColumn;
use App\DataTables\Column\DateTimeColumn;
use App\DataTables\Adapter\ORMAdapter;
use App\DataTables\DataTable;
use App\DataTables\DataTableFactory;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/load/plan')]
class LoadPlanController extends BaseController
{
    #[Route('/', name: 'load_plan.list')]
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
            ->add('natureOfTheCosting', TextColumn::class, [
                'label' => $translator->trans('load_plan.nature_of_the_costing', [], 'projects')
            ])
            ->add('weekNumber', TextColumn::class, [
                'label' => $translator->trans('load_plan.week_number', [], 'projects')
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
}
