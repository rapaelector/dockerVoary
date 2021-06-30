<?php

namespace App\Manager;

use App\Entity\Project;
use App\Entity\Relaunch;
use Doctrine\ORM\EntityManagerInterface;

class ProjectManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function save(Project $project) {
        $this->em->persist($project);
        $this->em->flush();
    }

    public function addRelaunch(Project $project) {
        $relaunch = new Relaunch();
        $project->setLastRelaunch($relaunch);
        $project->addRelaunch($relaunch);
        $this->save($project);
    }
}