<?php

namespace App\Entity\Traits;

use App\Utils\Resolver;
use App\Entity\Constants\Project;
use Symfony\Component\Serializer\Annotation\Groups;
trait ProjectEventTrait
{
    /**
     * @Groups({"projectEvent:scheduler"})
     */
    public function getResource()
    {
        return Resolver::resolve([$this, 'project', 'id'], null);
    }

    /**
     * @Groups({"projectEvent:scheduler"})
     */
    public function getBackgroundColor()
    {
        return array_key_exists($this->type, self::EVENT_COLORS) ? self::EVENT_COLORS[$this->type] : self::EVENT_DEFAULT_COLOR;
    }

    public function getPaymentWeeks()
    {
        $res = [];
        $start = clone $this->start;
        $end = clone $this->end;

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
                    'start' => $formattedStart,
                    'end' => $formattedEnd,
                ];
            }

            $start->modify('+1 week');
        };

        return $res;
    }

    /**
     * @Groups({"projectEvent:scheduler"})
     */
    public function getBubbleHtml()
    {
        $bubble = sprintf('
            <div class="text-center"> %s — %s </div>
            ', $this->start->format('Y/m/d'), $this->end->format('Y/m/d')
        );

        return $bubble;
    }
}