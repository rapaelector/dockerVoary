<?php

namespace App\Controller\Project;

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

use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/project/current/case')]
class ProjectController extends BaseController
{
    #[Route('/', name: 'project.current_case.list')]
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
                'label' => $translator->trans('columns.folder_name_on_the_server', [], 'projects')
            ])
            ->add('contact_name', TextColumn::class, [
                'field' => 'contact.lastName',
                'label' => $translator->trans('columns.contact_name', [], 'projects')
            ])
            ->add('id', TextColumn::class, [
                'label' => $translator->trans('action.action'),
                'render' => $this->actionsRenderer('client.list', 'current_case/_actions.html.twig'),
                'className' => 'text-center',
                'searchable' => false,
                'orderable' => false
            ])
        ;

        $table->createAdapter(ORMAdapter::class, [
            'entity' => Project::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('project')
                    ->from(Project::class, 'project')
                    ->leftJoin('project.contact', 'contact')
                    ->leftJoin('project.prospect', 'prospect')
                    ->distinct('project')
                ;
            }  
        ]);
        
        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('current_case/index.html.twig', [
            // 'projects' => $projectRepository->findAll(),
            'datatable' => $table,
        ]);
    }
}
