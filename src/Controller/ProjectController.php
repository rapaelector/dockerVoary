<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/project')]
class ProjectController extends BaseController
{
    /**
     * @isGranted("ROLE_PROJECT_VIEW")
     */
    #[Route('/', name: 'project.list', methods: ['GET', 'POST'])]
    #[Route('/', name: 'project_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        ProjectRepository $projectRepository, 
        TranslatorInterface $translator, 
        DataTableFactory $dataTableFactory
    ): Response
    {
        $createOptions = [
            'name' => 'list',
            'translation_domain' => 'project',
        ];

        $table =  $dataTableFactory->create([], $createOptions)
            ->add('siteCode', TextColumn::class, [
                'label' => $translator->trans('label.siteCode', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('project_siteCode')
                ),
            ])
        ;

        $table->createAdapter(ORMAdapter::class, [
            'entity' => Project::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('project')
                    ->from(Project::class, 'project')
                    ->distinct('project')
                ;
            }  
        ]);
        
        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
            'datatable' => $table,
        ]);
    }


    #[Route('/new', name: 'project.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'project.show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'project.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'project.delete', methods: ['POST'])]
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
