<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\Client\ClientServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/project')]
class ProjectController extends BaseController
{
    #[Route('/', name: 'project.list', methods: ['GET', 'POST'])]
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
                'label' => $translator->trans('columns.siteCode', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('projectSiteCode')
                ),
            ])
            ->add('prospect', TextColumn::class, [
                'field' => 'prospect.name',
                'label' => $translator->trans('label.prospect', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('prospect.name')
                ),
            ])
            ->add('user_email', TextColumn::class, [
                'field' => 'contact.email',
                'label' => $translator->trans('contact.label', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('contact.email')
                ),
            ])
            ->add('roadmap', TextColumn::class, [
                'label' => $translator->trans('columns.roadmap', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('projectRoadmap')
                ),
            ])
            ->add('amountSubcontractedWork', TextColumn::class, [
                'label' => $translator->trans('columns.amountSubcontractedWork', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('project_siteCode')
                ),
            ])
            ->add('amountBBISpecificWork', TextColumn::class, [
                'label' => $translator->trans('columns.amountBBISpecificWork', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('project_siteCode')
                ),
            ])
            ->add('globalAmount', TextColumn::class, [
                'label' => $translator->trans('columns.globalAmount', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class,
                    $this->filterOptionsProvider->getOptions('project_siteCode')
                ),
            ])
            ->add('id', TextColumn::class, [
                'label' => $translator->trans('label.action', [], 'project'),
                'render' => $this->actionsRenderer('client.list', 'project/_actions.html.twig'),
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

        // check if request get interlocuteurSelecs should be deleted or not


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $contactSelected = $form->get("contactSelect")->getData();
            // check if value is instance of user then add this into the project entity
            if ($contactSelected instanceof User) {
                $project->setContact($contactSelected);
            }
            // then check if valid
            if( $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($project);
                $contact = $project->getContact();
                $this->generatePassForContact($contact);
                $entityManager->flush();
                return $this->redirectToRoute('project.list');
            }
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    private function generatePassForContact(User &$contact) {
        if (null === $contact->getId()) {
            $mockPassword = md5($contact->getEmail());
            $contact->setPassword($mockPassword);
            /**
             * Type of user = external dont shown in the user list
             * canLogin = false avoid the access of the crm
             */
            $contact->setType(User::TYPE_EXTERNAL);
            $contact->setCanLogin(false);
        }
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

            return $this->redirectToRoute('project.list');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'project.delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Project $project, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
            if ($request->isXMLHttpRequest()) {
                return $this->json(['message' => $translator->trans('messages.delete_success', [], 'project')]);
            } else {
                $this->addFlash('success', $translator->trans('messages.delete_success', [], 'project'));
            }
        }

        return $this->redirectToRoute('project.list');
    }

    #[Route('/{id}/pdf', name: 'project.pdf')]
    public function pdf(Request $request, Project $project, Pdf $knpSnappyPdf)
    {
        $previewMode = $request->query->get('preview', false);
        $template = $this->renderView('project/pdf/index.html.twig', [
            'project' => $project,
            'previewMode' => $previewMode,
        ]);
        
        $options = [
            'margin-left' => '2mm',
            'margin-right' => '2mm',
            'margin-top' => '0mm',
        ];

        if ($previewMode) {
            return new Response($template);
        }
        
        return new PdfResponse($knpSnappyPdf->getOutputFromHtml($template, $options));
    }
}
