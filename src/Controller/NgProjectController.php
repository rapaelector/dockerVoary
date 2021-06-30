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

#[Route('/project')]
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

    #[Route('/autocomplete/data', name: 'project.ng.form_autocomplete_data', options: ['expose' => true])]
    public function getFormAutocompleteData(
        Request $request, 
        EntityManagerInterface $em, 
        SerializerInterface $serializer, 
        TranslatorInterface $translator
    )
    {
        $clients = $em->getRepository(Client::class)->findAll();
        $clientsFormatted = $serializer->serialize(
            $clients,
            'json',
            ['groups' => 'data-autocomplete']
        );
        $users = $em->getRepository(User::class)->findAll();
        $usersFormatted = $serializer->serialize(
            $users,
            'json',
            ['groups' => 'data-autocomplete']
        );
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

    #[Route('/{id}/exchange/history', name: '', options: ['expose' => true])]
    public function getExchangeHistory(Project $project, SerializerInterface $serializer)
    {
        $mockData = [
            [
                'date' => (new \DateTime())->format('d/m/Y'),
                'description' => 'lorem ipsum dolor sit amet',
                'color' => '#000',
            ],
            [
                'date' => (new \DateTime())->format('d/m/Y'),
                'description' => 'Idealy henintsoa',
                'color' => '#000',
            ],
            [
                'date' => (new \DateTime())->format('d/m/Y'),
                'description' => 'Henintsoa andrianirina',
                'color' => '#000',
            ],
            [
                'date' => (new \DateTime())->format('d/m/Y'),
                'description' => 'Test test',
                'color' => '#000',
            ],
        ]
        return $this->json(['data' => ])
    }
}