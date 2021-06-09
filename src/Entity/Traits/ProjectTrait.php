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
}