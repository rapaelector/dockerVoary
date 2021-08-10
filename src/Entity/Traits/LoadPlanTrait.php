<?php

namespace App\Entity\Traits;

trait LoadPlanTrait
{
    public function getStudyTime()
    {
        return self::STUDY_TIME;
    }
}