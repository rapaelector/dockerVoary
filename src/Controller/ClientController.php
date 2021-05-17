<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

#[Route('/client')]
class ClientController extends BaseController
{
    #[Route('/', name: 'client.list', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        ClientRepository $clientRepository, 
        TranslatorInterface $translator, 
        DataTableFactory $dataTableFactory
    ): Response
    {
        $createOptions = [
            'name' => 'list',
            'translation_domain' => 'client',
        ];

        $table =  $dataTableFactory->create([], $createOptions)
            ->add('clientNumber', TextColumn::class, [
                'label' => $translator->trans('label.client_number', [], 'client'),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('client_number')
                ),
            ])
            ->add('type', TextColumn::class, [
                'label' => $translator->trans('label.type', [], 'client'),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('client_type')
                ),
            ])
            ->add('name', TextColumn::class, [
                'label' => $translator->trans('label.name', [], 'client'),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('client_name')
                ),
            ])
            ->add('code_postal', TextColumn::class, [
                'field' => 'address.postalCode',
                'label' => $translator->trans('label.postal_code', [], 'client'),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('client_code_postal')
                ),
            ])
            ->add('country', TwigColumn::class, [
                'field' => 'address.country',
                'label' => $translator->trans('label.country', [], 'client'),
                'template' => 'shared/twig-columns/_country.html.twig',
                'className' => 'text-center',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('client_country')
                ),
            ])
            ->add('activity', TextColumn::class, [
                'label' => $translator->trans('label.activity', [], 'client'),
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('client_activity')
                ),
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'label' => $translator->trans('label.created_at', [], 'messages'),
                'format' => 'd/m/Y',
            ])
            ->add('id', TextColumn::class, [
                'label' => $translator->trans('label.action', [], 'client'),
                'render' => $this->actionsRenderer('client.list', 'client/_actions.html.twig'),
                'searchable' => false,
                'orderable' => false,
            ])
        ;

        $table->createAdapter(ORMAdapter::class, [
            'entity' => Client::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('client')
                    ->from(Client::class, 'client')
                    ->leftJoin('client.billingAddress', 'address')
                    ->distinct('client')
                ;
            }  
        ]);

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
            'datatable' => $table,
        ]);
    }

    #[Route('/new', name: 'client.new', methods: ['GET', 'POST'])]
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('messages.creation_success', [], 'client'));

            return $this->redirectToRoute('client.list');
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'client.show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'client.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('messages.editing_success', [], 'client'));

            return $this->redirectToRoute('client.list');
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'client.delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Client $client, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($client);
            $entityManager->flush();
            if ($request->isXMLHttpRequest()) {
                return $this->json(['message' => $translator->trans('messages.delete_success', [], 'client')]);
            } else {
                $this->addFlash('success', $translator->trans('messages.delete_success', [], 'client'));
            }
        }

        return $this->redirectToRoute('client.list');
    }
}
