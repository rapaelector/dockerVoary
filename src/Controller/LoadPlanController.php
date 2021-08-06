<?php

namespace App\Controller;

use App\Entity\LoadPlan;
use App\Entity\Project;
use App\Controller\BaseController;
use App\Form\LoadPlanType;
use App\DataTables\Column\TextColumn;
use App\DataTables\Column\TwigColumn;
use App\DataTables\Column\DateTimeColumn;
use App\DataTables\Adapter\ORMAdapter;
use App\DataTables\DataTable;
use App\DataTables\DataTableFactory;
use App\Service\Form\FormService;

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
                'label' => $translator->trans('load_plan.label.nature_of_the_costing', [], 'projects'),
                'render' => function ($value, $row) use ($translator) {
                    return $translator->trans('load_plan.task_type.' .$value, [], 'projects');
                },
            ])
            // ->add('weekNumber', TextColumn::class, [
            //     'label' => $translator->trans('load_plan.label.week_number', [], 'projects')
            // ])
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

    #[Route('/delete/{id}', name: 'load_plan.delete', methods: ['POST', 'DELETE'], options: ['expose' => true])]
    public function delete(Request $request, LoadPlan $loadPlan, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        if ($this->isCsrfTokenValid('delete'.$loadPlan->getId(), $request->request->get('_token'))) {
            $em->remove($loadPlan);
            $em->flush();

            return $this->json([
                'message' => $translator->trans('load_plan.messages.delete_successfull', [], 'projects')
            ]);
        }

        return $this->redirectToRoute('load_plan.index');
    }

    #[Route('/projects', name: 'load_plan.projects', options: ['expose' => true])]
    public function getProjects(Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $query = $request->query->get('q');
        $projects = $em->getRepository(Project::class)->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameters([
                'name' => '%' . $query . '%',
            ])
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ; 
        $normalizedProjects = $serializer->normalize($projects, 'json', ['groups' => 'loadPlan:create']);
        
        return $this->json($normalizedProjects);
    }

    #[Route('/config', name: 'load_plan.config', options: ['expose' => true])]
    public function getConfig(Request $request, TranslatorInterface $translator)
    {
        $taskTypesTranslated = [];
        foreach (LoadPlan::TASK_TYPES as $task) {
            $taskTypesTranslated[] = ['label' => $translator->trans('load_plan.task_type.' .$task, [], 'projects'), 'value' => $task];
        }

        return $this->json(['taskTypes' => $taskTypesTranslated]);
    }
}
