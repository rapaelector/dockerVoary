<?php

namespace App\Entity;

use App\Repository\RelaunchRepository;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"data-project"})
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
