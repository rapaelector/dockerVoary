<?php

namespace App\Service\ExchangeHistory;

use App\Entity\ExchangeHistory;

class ExchangeHistoryService
{
    public function getExchangeHistoryColor(ExchangeHistory $history)
    {
        if ($percentage = $history->getPercentage()) {
            $class =  ($percentage / 5) > 10 ? ceil($percentage / 5) : floor($percentage / 5);
            $classColor = $class ? 'exchange-completion-' .$class : 'exchange-completion-default';

            return $classColor;
        }

        return 'exchange-completion-default';
    }
}