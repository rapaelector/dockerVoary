<?php

namespace App\Service\Project;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ProjectEvent;

class ProjectEventService
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getMonthsLastWeeks(\DateTime $start = null, \DateTime $end = null)
    {
        $res = [];
        while ($start->getTimestamp() < $end->getTimestamp()) {
            $lastWeekOfTheMonth = null;
            if ($start->format('N') != 1) {
                $start->modify('next monday');
            }
            $weekEnd = clone $start;
            $weekEnd->modify('next sunday');

            // TODO : check if last monday of the month
            $startClone = clone $start;
            $nextWeek = $startClone->modify('+1 week');
            // Onlty store the last week of the month
            // start of the last week and end of the last week
            if ($start->format('Y-m') !== $nextWeek->format('Y-m')) {
                $endOfWeek = ((clone $start)->modify('next sunday'));
                $formattedStart = (clone $start)->format('Y-m-d') .'T'. (clone $start)->format('H:i:sP');
                $formattedEnd = (clone $endOfWeek)->format('Y-m-d') .'T'. (clone $endOfWeek)->format('H:i:sP');

                $res[] = [
                    'start' => (clone $start)->format('Y-m-d'),
                    'end' => $formattedEnd,
                ];
            }

            $start->modify('+1 week');
        };

        return $res;
    }

    public function getPaymentEvents(array $events = [])
    {
        $res = [];
        foreach ($events as $event) {
            $results = $this->getMonthsLastWeeks($event->getStart(), $event->getEnd());
            foreach ($results as $result) {
                $res[] = array_merge($result, [
                    'id' => uniqid($event->getId()),
                    'resource' => $event->getResource(),
                    'title' => 'aaa-' .$event->getId(),
                    'group' => 'payment',
                    'backgroundColor' => ProjectEvent::PAYMENT_BACKGROUND_COLOR,
                    'color' => ProjectEvent::PAYMENT_COLOR,
                ]);
            }
        }

        return $res;
    }
}