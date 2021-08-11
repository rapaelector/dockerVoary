<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\LoadPlan;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/load/plan/planning')]
class LoadPlanPlanningController extends BaseController
{
    #[Route('/', name: 'load_plan_planning.index')]
    public function index(Request $request)
    {
        return $this->render('load_plan_planning/index.html.twig');
    }

    #[Route('/resources', name: 'load_plan_planning.resources', options: ['expose' => true])]
    public function resources(Request $request, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $projects = $em->getRepository(LoadPlan::class)->getProjects();
        $normalizedProjects = $serializer->normalize($projects, 'json', ['groups' => 'loadPlan:planning']);
        $res = [];
        foreach ($normalizedProjects as $key => $project) {
            $res[$key]['id'] = $project['id'];
            $res[$key]['economist'] = $project['project']['economist'];
            $res[$key]['businessCharge'] = $project['project']['businessCharge'];
            $res[$key]['projectId'] = $project['project']['id'];
            $res[$key]['folderNameOnTheServer'] = $project['project']['folderNameOnTheServer'];
        }

        return $this->json(['resources' => $res]);
    }
}