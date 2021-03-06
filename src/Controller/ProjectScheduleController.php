<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectEvent;
use App\Entity\Constants\Project as ProjectConstants;
use App\Service\Project\ProjectEventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Serializer\Normalizer\DateTimeNormalizerCallback;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_POJECT_SCHEDULER_VIEW")
 */
#[Route('/project/scheduler')]
class ProjectScheduleController extends AbstractController
{
    #[Route('/', name: 'project_schedule')]
    public function index(): Response
    {
        return $this->render('project_schedule/index.html.twig');
    }

    #[Route('/resources', name: 'project_schedule.get_resources', options: ['expose' => true])]
    public function resources(
        Request $request, 
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        TranslatorInterface $translator
    )
    {
        $resources = $em->getRepository(Project::class)->getResources();
        $normalizedResources = $serializer->normalize($resources, 'json', ['groups' => 'project:scheduler-resource']);
        $shouldTranslatedFields = ['caseType'];
        
        foreach ($normalizedResources as $key => $resource) {
            $res = [];
            $normalizedResources[$key]['caseType'] = array_map(function ($val) use ($res, $translator) {
                return $res[] = $translator->trans($val, [], 'project');
            }, $normalizedResources[$key]['caseType']);
            $normalizedResources[$key]['marketType'] = $translator->trans($normalizedResources[$key]['marketType'], [], 'project');
        }

        return $this->json(['resources' => $normalizedResources]);
    }

    #[Route('/events', name: 'project_schedule.get_events', options: ['expose' => true])]
    public function events(
        Request $request, 
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        TranslatorInterface $translator,
        ProjectEventService $projectEventService
    )
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

        $events = $em->getRepository(ProjectEvent::class)->getEventsBetweenDate($start, $end);
        $normalizedEvents = $serializer->normalize($events, 'json', [
            'groups' => 'projectEvent:scheduler',
            \Symfony\Component\Serializer\Normalizer\AbstractNormalizer::CALLBACKS => [
                'start' => DateTimeNormalizerCallback::buildCallback('Y-m-d'),
                'end' => DateTimeNormalizerCallback::buildCallback('Y-m-d'),
            ],
        ]);

        $normalizedEventsFormatted = array_map(function ($event) {
            // Change the background here 
            $event['backgroundColor'] = '#00b050';
            
            return $event;
        }, $normalizedEvents);

        $paymentEvents = $projectEventService->getPaymentEvents($events)['paymentEvents'];
        $paymentTotals = $projectEventService->getPaymentEvents($events)['paymentTotals'];
        
        return $this->json([
            'events' => array_merge($normalizedEventsFormatted, $paymentEvents),
            // 'events' => array_merge($normalizedEvents, $paymentEvents),
            'totals' => [$paymentTotals],
        ]);
    }
}
