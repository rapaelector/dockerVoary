<?php

namespace App\Service\Project;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ProjectEvent;
use App\Utils\Resolver;

class ProjectEventService
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getPaymentEvents(array $events = [])
    {
        $res = [];
        $projects = [];
        
        foreach ($events as $event) {
            $projectId = Resolver::resolve([$event, 'project', 'id'], null);
            if (!array_key_exists($projectId, $projects)) {
                $projects[$projectId] = $event->getProject();
            }
        }

        foreach ($events as $event) {
            $results = $event->getPaymentWeeks();
            foreach ($results as $result) {
                $amount = $projects[$event->getProject()->getId()]->getWeekPaymentAmount();
                $res[] = array_merge($result, [
                    'id' => uniqid($event->getId()),
                    'resource' => $event->getResource(),
                    'title' => 'aaa-' .$event->getId(),
                    'group' => 'payment',
                    'backgroundColor' => ProjectEvent::PAYMENT_BACKGROUND_COLOR,
                    'color' => ProjectEvent::PAYMENT_COLOR,
                    'bubbleHtml' => number_format($amount, 2, ',', '.') .' €',
                ]);
            }
        }

        return $res;
    }
}