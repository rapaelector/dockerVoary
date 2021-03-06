<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientBusinessChargeType;
use App\Form\ClientType;
use App\Entity\Project;
use App\Form\ProjectBusinessChargeType;
use App\Repository\ClientRepository;
use App\Controller\BaseController;
use App\Service\Client\ClientServiceInterface;
use App\Security\Voter\ClientVoter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Use voter to check security
 */
#[Route('/client')]
class ClientController extends BaseController
{   
    /**
     * @isGranted("ROLE_CLIENT_VIEW")
     */
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
        $client = new Client();
        $businessChargeForm = $this->createForm(ClientBusinessChargeType::class, $client, array('csrf_protection' => false));

        $table =  $dataTableFactory->create([], $createOptions)
            ->add('clientNumber', TextColumn::class, [
                'label' => $translator->trans('label.client_number', [], 'client'),
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('client_number')
                ),
            ])
            ->add('clientName', TwigColumn::class, [
                'label' => $translator->trans('label.contact_name', [], 'client'),
                'template' => 'client/twig_columns/_contact_name.html.twig',
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('label.contact_name', [], 'project'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap',
                    ],
                ], true),
            ])
            ->add('name', TextColumn::class, [
                'label' => $translator->trans('label.prospect_name', [], 'client'),
                'className' => 'dynamic-nowrap',
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
//            ->add('country', TwigColumn::class, [
//                'field' => 'address.country',
//                'label' => $translator->trans('label.country', [], 'client'),
//                'template' => 'shared/twig-columns/_country.html.twig',
//                'className' => 'text-center',
//                'filter' => $this->filterBuilder->buildFilter(
//                    ChoiceFilter::class,
//                    array_merge(
//                        $this->filterOptionsProvider->getOptions('client_country'),
//                        ['choices' => $this->filterOptionsProvider->getClientCountries()
//                    ])
//                )
//            ])
            ->add('activity', TextColumn::class, [
                'label' => $translator->trans('label.activity', [], 'client'),
                'render' => function ($value, $context) use ($translator) {
                    return $value ? $translator->trans($value, [], 'client') : '';
                },
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    ChoiceFilter::class, 
                    array_merge(
                        $this->filterOptionsProvider->getOptions('client_activity'),
                        ['choices' => $this->filterOptionsProvider->getActivities()])
                ),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('businessCharge', TwigColumn::class, [
                'label' => $translator->trans('columns.businessCharge', [], 'project'),
                'template' => 'client/twig_columns/_prospect_business_charge.html.twig',
                'className' => 'dynamic-nowrap',
                'meta' => $this->columnMeta([
                    'abbr' => $translator->trans('columns.businessCharge', [], 'project'),
                    'label_attr' => [
                        'class' => 'dynamic-nowrap',
                    ],
                ], true),
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'label' => $translator->trans('label.created_at', [], 'messages'),
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
                )
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
                    ->leftJoin('client.contacts', 'contacts')
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
            'businessChargeForm' => $businessChargeForm->createView(),
        ]);
    }

    /**
     * Create new client is not check by voter because there are no client object when creating a new client
     * Cant send client object to ClientVoter because there are no client object here
     * 
     * @IsGranted("ROLE_CLIENT_ADD")
     */
    #[Route('/new', name: 'client.new', methods: ['GET', 'POST'])]
    public function new(Request $request, TranslatorInterface $translator, ClientServiceInterface $clientService): Response
    {
        $newClientNumber = $clientService->generateClientNumber();
        $client = new Client();
        $clientNumber = $newClientNumber[Client::TYPE_PROSPECT];
        $client->setClientNumber($clientNumber);
        $form = $this->createForm(ClientType::class, $client, [
            'validation_groups' => ['Default', 'client:create'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            /**
             * Le numero de prospect est automatis?? mais ne doit pas ??tre modifiable ou que par un utlisateur superadmin (skype)
             */
            $client->setClientNumber($clientNumber);
            /**
             * Job:
             *      - persist the client
             *      - loop for client contacts (users) and persist them
             *      - for each client contact, set type to external User::TYPE_EXTERNAL
             *      - for each client contact, denied access to the CRM by setting the canLogin to false 
             */
            $clientService->update($client);
            $this->addFlash('success', $translator->trans('messages.creation_success', [], 'client'));

            return $this->redirectToRoute('client.list');
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::VIEW'), client)")
     */
    #[Route('/{id}', name: 'client.show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * Edit client and redirect to the redirectPath if defined else redirect to the list of client
     * Role checked by voter
     * 
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), client)")
     * 
     * @param Request $request
     * @param Client $client
     * @param TranslatorInterface $translator
     * @param ClientServiceInterface $clientService
     */
    #[Route('/{id}/edit', name: 'client.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, TranslatorInterface $translator, ClientServiceInterface $clientService): Response
    {
        $clientNumber = $client->getClientNumber();
        $form = $this->createForm(ClientType::class, $client, [
            'validation_groups' => ['Default', 'client:edit'],
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Le numero de prospect est automatis?? mais ne doit pas ??tre modifiable ou que par un utlisateur superadmin (skype)
             */
            $client->setClientNumber($clientNumber);
            $clientService->update($client);
            $this->addFlash('success', $translator->trans('messages.editing_success', [], 'client'));
            /**
             * Check if redirectPath is defined
             * Redirection to the path request arguments
             */
            if ($redirectPath = $request->query->get('redirectPath')) {
                return $this->redirectToRoute($redirectPath, ['id' => $client->getid()]);
            }

            return $this->redirectToRoute('client.list');
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::DELETE'), client)")
     */
    #[Route('/{id}/delete', name: 'client.delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Client $client, TranslatorInterface $translator, ClientServiceInterface $clientService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $message = '';
            $type = '';
            $entityManager = $this->getDoctrine()->getManager();

            if (!$clientService->preprareClientRemovable($client)) {
                $message = $translator->trans('messages.delete_error', [], 'client');
                $type = $request->isXMLHttpRequest() ? 'error' : 'danger';
            } else {
                $type = 'success';
                $message = $translator->trans('messages.delete_success', [], 'client');
                $entityManager->remove($client);
                $entityManager->flush();
            }

            if ($request->isXMLHttpRequest()) {
                return $this->json(['message' => $message, 'type' => $type]);
            } else {
                $this->addFlash($type, $message);
            }
        }

        return $this->redirectToRoute('client.list');
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), client)")
     */
    #[Route('/business-charge/{id}', name: 'client.edit.business.charge', methods: ['POST'], options: ['expose' => true])]
    public function editBusinessCharge(Request $request, Client $client, TranslatorInterface $translator): Response
    {
        if ($request->isXmlHttpRequest()) {
            $tmpClient = new Client();
            $form = $this->createForm(ClientBusinessChargeType::class, $tmpClient, ['csrf_protection' => false]);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $client->setBusinessCharge($tmpClient->getBusinessCharge());
                $em = $this->getDoctrine()->getManager();
                $em->remove($tmpClient);
                $em->persist($client);
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

}
