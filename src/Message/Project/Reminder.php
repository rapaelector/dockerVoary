<?php

namespace App\Message\Project;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final class Reminder
{

    /**
     *  email address of user will receive the reminder
     */
    private $email;

    public function __construct(string $email, string $projectSiteCode)
    {
        $this->email = $email;
        $this->projectSiteCode = $projectSiteCode;
    }
    /**
     *
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    /**
     *
     */
    public function getProjectSiteCode(): string
    {
        return $this->projectSiteCode;
    }
}
