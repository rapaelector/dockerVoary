<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\User\ProfilType;
use App\Form\User\UserEditType;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use App\Message\User\UserCreatedMessage;
use App\Message\User\UserResetPasswordMessage;
use App\Utils\PasswordGenerator;

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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/user')]
class UserController extends BaseController
{

    #[Route('/get/{user}', name: 'project.get.json', methods: ['GET'])]
    public function getJson(User $user, SerializerInterface $serializer): Response
    {
        return new JsonResponse($serializer->serialize($user, 'json', ['groups' => 'project.user']));
    }

    /**
     * @IsGranted("ROLE_USER_VIEW")
     */
    #[Route('/', name: 'user.index')]
    public function index(Request $request, DataTableFactory $dataTableFactory, TranslatorInterface $translator): Response
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
            ->add('profileName', TwigColumn::class, [
                'label' => $translator->trans('label.profile', [], 'users'),
                'template' => 'shared/twig-columns/profile_template.html.twig',
                'searchable' => false,
                'orderable' => false,
            ])
            ->add('email', TextColumn::class, [
                'searchable' => true,
                'className' => 'dynamic-nowrap',
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('email')
                ),
            ])
            ->add('lastName', TextColumn::class, [
                'searchable' => true,
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('firstname')
                )
            ])
            ->add('firstName', TextColumn::class, [
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
            ->add('fax', TextColumn::class, [
                'filter' => $this->filterBuilder->buildFilter(
                    TextFilter::class, 
                    $this->filterOptionsProvider->getOptions('fax')
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
            ->add('updatedAt', DateTimeColumn::class, [
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
            ->add('canLogin', TwigColumn::class, [
                'template' => 'shared/twig-columns/_can_login.html.twig',
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
                    ->where('user.type = :type')
                    ->setParameter('type', User::TYPE_INTERNAL)
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

    /**
     * Dispatch a UserCreatedMessage event when form is submitted and valid
     * Access is given in the creation or edition user page
     * Into the event dispatched there are : 
     *      - send message to the user if the user can access the CRM from a checkbox
     *      - the contents of the message is the email of the user and a plain password
     *      - avoid sending the message if the user have not access to the CRM from the same checkbox
     *      - event location in App\MessageHandler\User\UserCreatedMessageHandler
     * 
     * @IsGranted("ROLE_USER_ADD")
     */
    #[Route('/new', name: 'user.new', methods: ['GET','POST'])]
    public function new(Request $request, UserPasswordEncoderInterface $encoder, MessageBusInterface $messagerBus, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'validation_groups' => ['Default', 'user:create'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $userPlainPassword = $user->getPassword();
            $encodePassword = $encoder->encodePassword($user, $userPlainPassword);
            $user->setPassword($encodePassword);
            $entityManager->persist($user);
            $entityManager->flush();

            /**
             * - Only send message (email) for the user if the user have access to the CRM
             * - access is given in the creation or edition user page
             */
            if ($user->getCanLogin()) {
                /**
                 * This event send message only till now
                 */
                $messagerBus->dispatch(new UserCreatedMessage($user->getId(), $userPlainPassword));
            }

            return $this->redirectToRoute('user.index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::VIEW'), user)")
     */
    #[Route('/{id}', name: 'user.show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), user)")
     */
    #[Route('/{id}/edit', name: 'user.edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $user, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('messages.user_edit_success', [], 'users'));
            
            return $this->redirectToRoute('user.index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'user.delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, User $user, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted(constant('\\App\\Security\\Voter\\Attributes::DELETE'), $user);
        
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            if ($request->isXMLHttpRequest()) {
                return $this->json(['message' => $translator->trans('messages.delete_success', [], 'users')]);
            } else {
                $this->addFlash('success', $translator->trans('messages.delete_success', [], 'users'));
            }
        }

        return $this->redirectToRoute('user.index');
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), user)")
     */
    #[Route('/{id}/reset-password', name: 'user.reset_password', methods: ['POST'], options: ['expose'=>true])]
    public function resetPassword(Request $request, User $user, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, MessageBusInterface $messagerBus)
    {
        if ($request->isXmlHttpRequest()) {
            $userPlainPassword = PasswordGenerator::randomString();
            $encodedPassword = $encoder->encodePassword($user, $userPlainPassword);
            $user->setPassword( $encodedPassword);
            $em->flush();
            $messagerBus->dispatch(new UserResetPasswordMessage($user->getId(), $userPlainPassword));

            return $this->json(['message' => 'data send ok'], 200);
        }

        return $this->json(['message' => 'no XMLHttpRequest request'], 400);
    }
}
