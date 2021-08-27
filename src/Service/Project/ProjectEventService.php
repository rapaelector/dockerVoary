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
        $totals = [];
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
                    'title' => $amount,
                    'group' => 'payment',
                    'backgroundColor' => ProjectEvent::PAYMENT_BACKGROUND_COLOR,
                    // 'color' => ProjectEvent::PAYMENT_COLOR,
                    'color' => $event->color,
                    'bubbleHtml' => number_format($amount, 2, ',', '.') .' €',
                ]);
                
                $date = (\DateTime::createFromFormat('Y-m-d', explode('T', $result['start'])[0]));
                $key = (clone $date)->format('Y-m');

                if (!array_key_exists($key, $totals)) {
                    $totals[$key] = [
                        'amount' => 0,
                        'year' => $date->format('Y'),
                        'month' => $date->format('m'),
                    ];
                }
                $totals[$key]['amount'] += $amount;
            }
        }

        return [
            'paymentEvents' => $res,
            'paymentTotals' => array_values($totals),
        ];
    }
}