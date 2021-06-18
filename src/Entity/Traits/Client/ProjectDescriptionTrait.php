<?php

namespace App\Entity\Traits\Client;

use App\Entity\Constants\Project;

trait ProjectDescriptionTrait
{
    public function checkMarketType()
    {
        if ($this->marketType) {
            return Project::FOLDER_PROSPECTION_MARKET_TYPE_CHOICE;
        }

        return '';
    }
}