<?php

namespace App\Utils;

final class DateHelper
{
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
}