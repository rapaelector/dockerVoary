<?php

namespace App\Message\Project;

use App\Entity\Project;
use App\Entity\Action;

final class PreValidateProject
{
    /** @var Project $project */
    private $project;

    /** @var Action $action */
    private $action;

    /** @var int $actionId */
    private $actionId;

    public function __construct(Project $project, Action $action)
    {
        $this->project = $project;
        $this->action = $action;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setActionId()
    {
        $this->actionId = null;
        if ($this->action) {
            $this->actionId = $this->action->getId(); 
        }
    }

    public function getActionId()
    {
        return $this->actionId;
    }
}