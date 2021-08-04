<?php

namespace App\Entity\Traits;

trait ProjectTrait
{
    public function getPdfName(): string
    {
        if ($this->getProspect()) {
            return 'Projet-' .$this->getId(). '-' .$this->getProspect()->getClientNumber(). '.pdf';
        }

        return 'Projet-' .$this->getId(). '.pdf';
    }

    public function getProvisionalAmount()
    {
        return $this->globalAmount;
    }

    public function getPaymentWeeks()
    {
        $res = [];
        foreach ($this->events as $event) {
            $res = array_merge($res, $event->getPaymentWeeks()); 
        }

        return $res;
    }

    public function getWeekPaymentAmount()
    {
        $getPaymentWeeksCount = count($this->getPaymentWeeks());
        
        return $getPaymentWeeksCount > 0 ? ($this->getProvisionalAmount() / $getPaymentWeeksCount) : $this->getProvisionalAmount();
    }
}