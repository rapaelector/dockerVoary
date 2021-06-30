<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Relaunch;
use App\Entity\User;
use App\Form\EconomistFormType;
use App\Form\ProjectBusinessChargeType;
use App\Form\ProjectType;
use App\Manager\ProjectManager;
use App\Repository\ProjectRepository;
use App\Service\Client\ClientServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    /**
     * @isGranted("ROLE_PROJECT_VIEW")
     */
    #[Route('/', name: 'project.list', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        ProjectRepository $projectRepository, 
        TranslatorInterface $translator, 
        DataTableFactory $dataTableFactory
    ): Response
    {

        // create fake project to catch the businessCharge
        $project = new Project();
        $businessChargeForm = $this->createForm(ProjectBusinessChargeType::class, $project, array('csrf_protection' => false));
        $economistForm = $this->createForm(EconomistFormType::class, $project, array('csrf_protection' => false));

        $createOptions = [
            'name' => 'list',
            'translation_domain' => 'project',
        ];

        $table =  $dataTableFactory->create([], $createOptions)
            ->add('siteCode', TextColumn::class, [
                'label' => $translator->trans('columns.siteCode', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class, 
                    array_merge(
                        $this->filterOptionsProvider->getOptions('site_code'),
                        ['choices' => $this->filterOptionsProvider->getProjectSiteCode()]
                    )
                ),
                'searchable' => true,
                'meta' => [
                    'abbr' => $translator->trans('columns.siteCode_raw', [], 'project'),
                ]
            ])
            ->add('marketType', TextColumn::class, [
                'label' => $translator->trans('columns.market_type', [], 'projects'),
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
                ], false),
                'searchable' => true,
            ])
            ->add('prospect', TextColumn::class, [
                'field' => 'prospect.name',
                'label' => $translator->trans('label.prospect', [], 'project'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class, 
                    array_merge(
                        $this->filterOptionsProvider->getOptions('prospect'),
                        ['choices' => $this->filterOptionsProvider->getProjectProspect()]
                    )
                ),
                'searchable' => true,
            ])
            ->add('business_charge', TwigColumn::class, [
                'label' => $translator->trans('columns.businessCharge', [], 'project'),
                'template' => 'project/twig_columns/_business_charge.html.twig',
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.businessCharge', [], 'project'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap',
                    ],
                ], true),
            ])
            ->add('economist', TwigColumn::class, [
                'label' => $translator->trans('columns.economist', [], 'project'),
                'template' => 'project/twig_columns/_economist.html.twig',
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.economist', [], 'project'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap',
                    ],
                ], true),
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
            ->add('roadmap', TextColumn::class, [
                'label' => $translator->trans('columns.roadmap', [], 'project'),
                'className' => 'dynamic-nowrap text-center',
                'render' => function ($value, $row) use ($translator) {
                    return $value ? $translator->trans('columns.yes', [], 'project') : (($value == false || $value == 0) ? $translator->trans('columns.no', [], 'project') : '');
                },
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class, 
                    array_merge(
                        $this->filterOptionsProvider->getOptions('roadmap'),
                        ['choices' => [
                            true => $translator->trans('label.yes', [], 'projects'),
                            false => $translator->trans('label.no', [], 'projects'),
                        ]]
                    )
                ),
                'searchable' => true,
            ])
            ->add('amountSubcontractedWork', TextColumn::class, [
                'label' => $translator->trans('columns.amount_subcontracted_work', [], 'project'),
                'className' => 'dynamic-nowrap text-right',
                'render' => $this->numberFormatFactory(0, ',', ' '),
                // 'filter' => $this->filterBuilder->buildFilter(
                //     TextFilter::class,
                //     $this->filterOptionsProvider->getOptions('project_siteCode')
                // ),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.amount_subcontracted_work_abbr', [], 'project'),
                ]),
            ])
            ->add('amountBBISpecificWork', TextColumn::class, [
                'label' => $translator->trans('columns.amount_bbi_specific_work', [], 'project'),
                'className' => 'dynamic-nowrap text-right',
                'render' => $this->numberFormatFactory(0, ',', ' '),
                // 'filter' => $this->filterBuilder->buildFilter(
                //     TextFilter::class,
                //     $this->filterOptionsProvider->getOptions('project_siteCode')
                // ),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.amount_bbi_specific_work_abbr', [], 'project'),
                ]),
            ])
            ->add('globalAmount', TextColumn::class, [
                'label' => $translator->trans('columns.global_amount', [], 'project'),
                'className' => 'dynamic-nowrap text-right',
                'render' => $this->numberFormatFactory(0, ',', ' '),
                // 'filter' => $this->filterBuilder->buildFilter(
                //     TextFilter::class,
                //     $this->filterOptionsProvider->getOptions('project_siteCode')
                // ),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.global_amount_abbr', [], 'project'),
                ]),
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'label' => $translator->trans('columns.first_ask_date', [], 'project'),
                'format' => 'd/m/Y',
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    DateRangeFilter::class,
                    [
                        'type' => 'daterange',
                        'attr' => [
                            'data-ranges' => 'weeks',
                        ],
                    ]
                ),
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.first_ask_date_abbr', [], 'project'),
                ]),
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
                    ->leftJoin('project.contact', 'contact')
                    ->leftJoin('project.prospect', 'prospect')
                    ->leftJoin('project.businessCharge', 'businessCharge')
                    ->leftJoin('project.economist', 'economist')
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
            'businessChargeForm' => $businessChargeForm->createView(),
            'economistForm' => $economistForm->createView()
        ]);
    }

    /**
     * @isGranted("ROLE_PROJECT_ADD")
     */
    #[Route('/new', name: 'project.new', methods: ['GET', 'POST'])]
    public function new(Request $request, TranslatorInterface $translator): Response
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
                $this->addFlash('success', $translator->trans('messages.creation_success', [], 'project'));
                
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

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::VIEW'), project)")
     */
    #[Route('/{id}/show', name: 'project.show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), project)")
     */
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

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), project)")
     */
    #[Route('/business-charge/{id}', name: 'project.edit.business.charge', methods: ['POST'], options: ['expose' => true])]
    public function editBusinessCharge(Request $request, Project $project, TranslatorInterface $translator): Response
    {
        if ($request->isXmlHttpRequest()) {
            $tmpProject = new Project();
            $form = $this->createForm(ProjectBusinessChargeType::class, $tmpProject, ['csrf_protection' => false]);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $project->setBusinessCharge($tmpProject->getBusinessCharge());
                $em = $this->getDoctrine()->getManager();
                $em->remove($tmpProject);
                $em->persist($project);
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse([
                    'type' => 'success',
                    'message' => $translator->trans('business_charge.success', [], 'projects')
                ], Response::HTTP_OK);
            }

            return new JsonResponse([
                'type' => 'error',
                'message' => $translator->trans('business_charge.error', [], 'projects')
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), project)")
     */
    #[Route('/economist/{id}', name: 'project.edit.economist', methods: ['POST'], options: ['expose' => true])]
    public function editEconomist(Request $request, Project $project, TranslatorInterface $translator): Response
    {
        if ($request->isXmlHttpRequest()) {
            $tmpProject =new Project();
            $form = $this->createForm(EconomistFormType::class, $tmpProject, ['csrf_protection' => false]);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $project->setEconomist($tmpProject->getEconomist());
                $em = $this->getDoctrine()->getManager();
                $em->remove($tmpProject);
                $em->persist($project);
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse([
                    'type' => 'success',
                    'message' => $translator->trans('economist.success', [], 'projects')
                ], Response::HTTP_OK);
            }

            return new JsonResponse([
                'type' => 'error',
                'message' => $translator->trans('economist.error', [], 'projects')
            ], Response::HTTP_SERVICE_UNAVAILABLE);

        }
    }

    /**
     * List all errors of a given bound form.
     *
     * @param Form $form
     *
     * @return array
     */
    protected function getFormErrors(FormInterface $form)
    {
        $errors = array();

        // Global
        foreach ($form->getErrors(true, true) as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::DELETE'), project)")
     */
    #[Route('/{id}/delete', name: 'project.delete', methods: ['POST', 'DELETE'])]
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

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), project)")
     */
    #[Route('/{id}/reminder', name: 'project.reminder', methods: ['POST', 'PUT'])]
    public function reminder(
        Request $request,
        Project $project,
        TranslatorInterface $translator,
        MessageBusInterface $bus,
        ProjectManager $projectManager
    ): Response
    {
        if ($this->isCsrfTokenValid('reminder'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            // send relaunch message
            $bus->dispatch(new \App\Message\Project\Reminder($project->getContact()->getEmail(), $project->getSiteCode()));
            // then add new relaunch
            $projectManager->addRelaunch($project);
            if ($request->isXMLHttpRequest()) {
                return $this->json(['message' => $translator->trans('messages.reminder_success', [], 'projects')]);
            } else {
                $this->addFlash('success', $translator->trans('messages.reminder_success', [], 'projects'));
            }
        }

        return $this->redirectToRoute('project.list');
    }

    #[Route('/{id}/pdf', name: 'project.pdf', methods: ['GET'])]
    public function pdf(Request $request, Project $project, Pdf $knpSnappyPdf)
    {
        $previewMode = $request->query->get('preview', false);
        $template = $this->renderView('project/pdf/index.html.twig', [
            'project' => $project,
            'previewMode' => $previewMode,
        ]);
        $pdfName = $project->getPdfName();

        $options = [
            'margin-left' => '2mm',
            'margin-right' => '2mm',
            'margin-top' => '7mm',
            'margin-bottom' => '10mm',
        ];
        
        if (!$request->query->get('nofooter')) {
            $options['footer-html'] = $this->generateUrl('project.pdf_footer', [
                'id' => $project->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }
        if ($previewMode) {
            return new Response($template);
        }

        return new PdfResponse($knpSnappyPdf->getOutputFromHtml($template, $options), $pdfName);
    }

    #[Route('/{id}/pdf-footer', name: 'project.pdf_footer')]
    public function generateFooter(Project $project)
    {
        return $this->render('project/pdf/_pdf_footer.html.twig', ['project' => $project]);
    }

}
