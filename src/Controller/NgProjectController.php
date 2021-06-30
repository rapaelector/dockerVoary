<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Constants\Project as ProjectConstants;
use App\Form\ProjectEditType;
use App\Form\Project\NgProjectType;
use App\Service\Form\FormService;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Intl\Countries;

#[Route('/project/ng')]
class NgProjectController extends BaseController
{
    #[Route('/{id}/edit', name: 'project.ng.project_edit', methods: ['POST', 'GET'], options: ['expose' => true])]
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
        \App\Service\User\UserService $userService
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
}