<?php

namespace App\Entity\Traits;

trait AddressTrait
{
    public function __toString()
    {
        return $this->name;
    }
}