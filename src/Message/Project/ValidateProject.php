<?php

namespace App\Message\Project;

use App\Entity\Project;

final class ValidateProject
{
    /** @var Project $project */
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }
}