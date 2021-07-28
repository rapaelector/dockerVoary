<?php

namespace App\Controller;

use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/project/scheduler')]
class ProjectScheduleController extends AbstractController
{
    #[Route('/', name: 'project_schedule')]
    public function index(): Response
    {
        return $this->render('project_schedule/index.html.twig');
    }

    #[Route('/resources', name: 'project_schedule.get_resources', options: ['expose' => true])]
    public function getResources(
        Request $request, 
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        TranslatorInterface $translator
    )
    {
        $sites = $em->getRepository(Project::class)->getSites();
        $normalizedSites = $serializer->normalize($sites, 'json', ['groups' => 'scheduler-data']);
        $shouldTranslatedFields = ['caseType'];

        foreach ($normalizedSites as $key => $site) {
            $res = [];
            $normalizedSites[$key]['caseType'] = array_map(function ($val) use ($res, $translator) {
                return $res[] = $translator->trans($val, [], 'project');
            }, $normalizedSites[$key]['caseType']);
            $normalizedSites[$key]['marketType'] = $translator->trans($normalizedSites[$key]['marketType'], [], 'project');
        }

        return $this->json([
            'resources' => $normalizedSites,
        ]);
    }
}
