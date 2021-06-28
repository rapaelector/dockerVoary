<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Project;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Intl\Countries;

#[Route('/project/ng')]
class NgProjectController extends BaseController
{
    #[Route('/{id}/edit', name: 'project.ng.index')]
    public function index(Request $request, Project $project)
    {
        return $this->render('project/ng/index.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/autocomplete/data', name: 'project.ng.form_autocomplete_data', options: ['expose' => true])]
    public function getFormAutocompleteData(Request $request, EntityManagerInterface $em, SerializerInterface $serializer)
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
        ]);
    }

    #[Route('/{id}', name: 'project.ng.get_project', options: ['expose' => true])]
    public function getProject(Project $project, SerializerInterface $serializer)
    {
        $projectFormatted = $serializer->serialize(
            $project,
            'json',
            ['groups' => 'data-project']
        );

        return $this->json(['project' => $projectFormatted]);
    }
}