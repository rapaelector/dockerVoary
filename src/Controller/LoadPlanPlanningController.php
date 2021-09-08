<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\LoadPlan;
use App\Entity\Project;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Serializer\Normalizer\DateTimeNormalizerCallback;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_LOAD_PLAN_VIEW")
 */
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
        $projectIds = $em->getRepository(LoadPlan::class)->getProjectIds();
        $projects = $em->getRepository(Project::class)->getProjectsByIds($projectIds);

        $normalizedProjects = $serializer->normalize($projects, 'json', ['groups' => 'loadPlan:planning']);
        $res = [];
        foreach ($normalizedProjects as $key => $project) {
            $res[$key]['id'] = $project['id'];
            $res[$key]['economist'] = $project['economist'];
            $res[$key]['businessCharge'] = $project['businessCharge'];
            // $res[$key]['loadPlanId'] = $project['id'];
            $res[$key]['folderNameOnTheServer'] = $project['folderNameOnTheServer'];
        }

        return $this->json(['resources' => $res]);
    }

    #[Route('/events', name: 'load_plan_planning.events', options: ['expose' => true])]
    public function events(Request $request, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $resEvents = [];
        $start = $request->query->get('start');
        $end = $request->query->get('end');

        if (!$start) {
            $start_ = (new \DateTIme())->format('Y') . '-01-01';
            $start = (\DateTime::createFromFormat('Y-m-d', $start_))->format('Y-m-d');
        }
        
        if (!$end) {
            $end_ = (new \DateTime())->format('Y') . '-12-31';
            $end = (\DateTime::createFromFormat('Y-m-d', $end_))->format('Y-m-d');
        }

        $start = \DateTime::createFromFormat('Y-m-d', $start);
        $end = \DateTime::createFromFormat('Y-m-d', $end);
        
        $events = $em->getRepository(LoadPlan::class)->getEventsBetweenDate($start, $end);
        $normalizedEvents = $serializer->normalize($events, 'json', [
            'groups' => 'loadPlan:planning-event',
            \Symfony\Component\Serializer\Normalizer\AbstractNormalizer::CALLBACKS => [
                'start' => DateTimeNormalizerCallback::buildCallback('Y-m-d'),
                'end' => DateTimeNormalizerCallback::buildCallback('Y-m-d'),
            ],
        ]);

        $res = [];
        foreach ($normalizedEvents as $key => $event) {
            $res[$key]['id'] = $event['id'];
            $res[$key]['resource'] = $event['project']['id'];
            $res[$key]['title'] = array_key_exists($event['natureOfTheCosting'], LoadPlan::TASK_TYPES_PLANNING) ? LoadPlan::TASK_TYPES_PLANNING[$event['natureOfTheCosting']] : $event['natureOfTheCosting'];
            $res[$key]['backgroundColor'] = array_key_exists($event['natureOfTheCosting'], LoadPlan::TASK_TYPES_PLANNING_COLORS) ? LoadPlan::TASK_TYPES_PLANNING_COLORS[$event['natureOfTheCosting']] : $event['natureOfTheCosting'];
            $res[$key]['color'] = '#000000';
            $res[$key]['bubbleHtml'] = '';
            $res[$key]['start'] = $event['start'];
            $res[$key]['end'] = $event['end'];
        }
        
        return $this->json(['events' => $res]);
    }
}