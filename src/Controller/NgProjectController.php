<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Constants\Project as ProjectConstants;
use App\Form\ProjectEditType;
use App\Form\Project\NgProjectType;
use App\Form\User\ContactType;
use App\Service\Form\FormService;
use App\Service\User\UserService;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Intl\Countries;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/project')]
class NgProjectController extends BaseController
{
    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), project)")
     */
    #[Route('/{id}/follow-up', name: 'project.ng.project_follow_up', methods: ['POST', 'GET'], options: ['expose' => true])]
    public function index(
        Request $request, 
        Project $project, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em, 
        TranslatorInterface $translator,
        FormService $formService
    )
    {
        if ($request->getMethod() == 'POST') {
            $form = $this->createForm(NgProjectType::class, $project, [
                'csrf_protection' => false,
                'allow_extra_fields' => true,
            ]);
            $form->submit(json_decode($request->getContent(), true));
            
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                return $this->json([
                    'message' => $translator->trans('messages.editing_success', [], 'project'),
                    'data' => ['project' => $serializer->normalize($project, 'json', ['groups' => 'data-project'])],
                ]);
            }

            return $this->json([
                'message' => $translator->trans('messages.editing_failed', [], 'project'),
                'errors' => $formService->getErrorsFromForm($form),
            ], 400);
        }

        return $this->render('project/ng/index.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/get/data', name: 'project.ng.form_data', options: ['expose' => true])]
    public function getFormData(
        Request $request, 
        EntityManagerInterface $em, 
        SerializerInterface $serializer, 
        TranslatorInterface $translator,
        UserService $userService
    )
    {
        $clients = $em->getRepository(Client::class)->findAll();
        $clientsFormatted = $serializer->normalize(
            $clients,
            'json',
            ['groups' => 'project-form-data']
        );
        $users = $em->getRepository(User::class)->findAll();
        $usersFormatted = $serializer->normalize(
            $users,
            'json',
            ['groups' => 'project-form-data']
        );
        foreach ($users as $i => $user) {
            $usersFormatted[$i]['avatar'] = $userService->getUserAvatar($user);
        }
        $countries = Countries::getNames();
        $icountries = array_flip($countries);
        $disaSheetsValidation = array_map(function ($disaFile) use ($translator) {
            return ['value' => $disaFile, 'label' => $translator->trans($disaFile, [], 'project')];
        }, ProjectConstants::TYPE_DISA_SHEET);

        return $this->json([
            'clients' => $clientsFormatted,
            'users' => $usersFormatted,
            'economists' => $usersFormatted,
            'businessCharge' => $usersFormatted,
            'countries' => array_values(array_map(function ($countryName) use($icountries) {
                return ['name' => $countryName, 'countryCode' => $icountries[$countryName]];
            }, $countries)),
            'caseTypes' => array_map(function ($caseType) use ($translator) {
                return ['value' => $caseType, 'label' => $translator->trans($caseType, [], 'project')];
            }, ProjectConstants::CASE_TYPES),
            'disaSheetsValidation' => $disaSheetsValidation,
        ]);
    }

    #[Route('/{id}', name: 'project.ng.get_project', options: ['expose' => true])]
    public function getProject(Project $project, SerializerInterface $serializer)
    {
        $projectFormatted = $serializer->normalize(
            $project,
            'json',
            ['groups' => 'data-project']
        );

        return $this->json(['project' => $projectFormatted]);
    }

    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), project), or @isGranted('ROLE_PROJECT_VIEW')")
     */
    #[Route('/create/contact', name: 'project.ng.create_contact', options: ['expose' => true])]
    public function createContact(
        Request $request, 
        EntityManagerInterface $em, 
        TranslatorInterface $translator, 
        SerializerInterface $serializer, 
        FormService $formService,
        UserService $userService
    )
    {
        $contact = new User();
        $form = $this->createForm(ContactType::class, $contact,[
            'csrf_protection' => false,
        ]);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isSubmitted() && $form->isValid()) {
            $mockPassword = md5($contact->getEmail());
            $contact->setPassword($mockPassword);

            $em->persist($contact);
            $em->flush();

            $contactFormatted = $serializer->normalize(
                $contact,
                'json',
                ['groups' => 'project-form-data']
            );
            $contactFormatted['avatar'] = $userService->getUserAvatar($contact);
            return $this->json([
                'message' => $translator->trans('messages.contact_create_success', [], 'project'), 
                'data' => $contactFormatted
            ]);
        }

        return $this->json([
            'message' => $translator->trans('messages.contact_creation_failed', [], 'project'),
            'errors' => $formService->getErrorsFromForm($form),
        ], 400);
    }
}