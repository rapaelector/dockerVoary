<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\DataTables\Column\TextColumn;
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

#[Route('/user')]
class UserController extends BaseController
{
    #[Route('/', name: 'user.index')]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $createOptions = [
            'name' => 'list',
            'translation_domain' => 'users',
        ];

        /**
         * @var DataTable
         * The default column label will be
         * table_name.columns.columnKey
         * e.g: list.columns.email
         */
        $table =  $dataTableFactory->create([], $createOptions)
        // $table =  $dataTableFactory->create()
            ->add('email', TextColumn::class, [
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('email')
                ),
            ])
            ->add('firstName', TextColumn::class, [
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('firstname')
                )
            ])
            ->add('lastName', TextColumn::class, [
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('lastname')
                )
            ])
            ->add('phone', TextColumn::class, [
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('lastname')
                )
            ])
            ->add('job', TextColumn::class, [
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('lastname')
                )
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'searchable' => true,
                'format' => 'd/m/y',
                'filter' => $this->filterBuilder->buildFilter(
                    DateRangeFilter::class, 
                    [
                        'type' => 'daterange',
                    ]
                    // $this->filterOptionsProvider->getOptions('lastname')
                )
            ])
            ->add('id', TextColumn::class, [
                'label' => 'Actions', 
                'render' => $this->actionsRenderer('user.index', 'user/_actions.html.twig'),
                'orderable' => false,
                'className' => 'py-0 text-center flotte-action',
            ])
        ;

        $table->createAdapter(ORMAdapter::class, [
            'entity' => User::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('user')
                    ->from(User::class, 'user')
                    ->distinct('user')
                ;
            }  
        ]);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('user/index.html.twig', [
            'clientId' => 0,
            'datatable' => $table,
        ]);
    }

    #[Route('/new', name: 'user.new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user.index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user.show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user.edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user.index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user.delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user.index');
    }
}
