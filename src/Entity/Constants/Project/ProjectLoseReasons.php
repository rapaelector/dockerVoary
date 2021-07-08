<?php

namespace App\Entity\Constants\Project;

class ProjectLoseReasons
{
    const THE_CLIENT_ABANDONS_THE_PROJECT = 'the_client_abandons_the_project';
    const WE_ARE_TOO_EXPENSIVE = 'we_are_too_expensive';
    const THE_TECHNICAL_SOLUTION = 'the_technical_solution';
    const LACK_OF_RESPONSIVENESS = 'lack_of_responsiveness';
    const OTHER = 'other';

    const PROJECT_LOSE_REASONS = [
        self::THE_CLIENT_ABANDONS_THE_PROJECT,
        self::WE_ARE_TOO_EXPENSIVE,
        self::THE_TECHNICAL_SOLUTION,
        self::LACK_OF_RESPONSIVENESS,
        self::OTHER,
    ];
}