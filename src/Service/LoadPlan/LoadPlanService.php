<?php

namespace App\Service\LoadPlan;

use App\Entity\LoadPlan;
use Doctrine\ORM\EntityManagerInterface;

class LoadPlanService
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEconomistWeeklyStudyTime(\DateTime $start, \DateTime $end)
    {
        $economistWeeklyStudyTime = $this->em->getRepository(LoadPlan::class)->getWeeklyStudyTimeCountPerEconomist($start, $end);

        return $economistWeeklyStudyTime;
    }
}