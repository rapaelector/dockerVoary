<?php

namespace App\Entity;

use App\Repository\RelaunchRepository;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RelaunchRepository::class)
 */
class Relaunch
{
    use BlameableTrait;
    use SoftDeleteableTrait;
    use TimestampableTrait;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
