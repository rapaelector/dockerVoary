<?php

namespace App\Utils;

final class DateHelper
{
    private $dayHoursDuration;

    public function __construct(int $dayHoursDuration)
    {
        $this->dayHoursDuration = $dayHoursDuration;
    }

    public function getStartOfWeek(\DateTime $date): \DateTime
    {
        /** @var \DateTime */
        $tmp = clone $date;

        while ($tmp->format('N') != 1) {
            $tmp->modify('-1 day');
        }

        return $tmp;
    }

    public function getEndOfWeek(\DateTime $date): \DateTime
    {
        /** @var \DateTime */
        $tmp = clone $date;

        // while ($tmp->format('N') != 0) {
        while ($tmp->format('N') != 7) {
            $tmp->modify('+1 day');
        }

        return $tmp;
    }

    public function hoursToDay(float $hours = null): float
    {
        if (is_null($hours)) {
            return 0;
        }

        return $hours / $this->dayHoursDuration;
    }
}