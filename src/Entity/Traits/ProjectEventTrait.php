<?php

namespace App\Entity\Traits;

use App\Utils\Resolver;
use App\Entity\Constants\Project;
use Symfony\Component\Serializer\Annotation\Groups;
trait ProjectEventTrait
{
    /**
     * @Groups({"projectEvent:scheduler"})
     */
    public function getResource()
    {
        return Resolver::resolve([$this, 'project', 'id'], null);
    }

    /**
     * @Groups({"projectEvent:scheduler"})
     */
    public function getBackgroundColor()
    {
        return array_key_exists($this->type, self::EVENT_COLORS) ? self::EVENT_COLORS[$this->type] : self::EVENT_DEFAULT_COLOR;
    }
}