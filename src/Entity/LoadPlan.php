<?php

namespace App\Entity;

use App\Repository\LoadPlanRepository;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PLAN DE CHARGE ECONOMISTE
 * 
 * SoftDeleteable annotation must be used with SoftDeleteableTrait
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass=LoadPlanRepository::class)
 */
class LoadPlan
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

    /**
     * Nature du chiffrage
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $natureOfTheCosting;

    /**
     * N° Semaine pour remise de l'etude
     * 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weekNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="loadPlans")
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNatureOfTheCosting(): ?string
    {
        return $this->natureOfTheCosting;
    }

    public function setNatureOfTheCosting(?string $natureOfTheCosting): self
    {
        $this->natureOfTheCosting = $natureOfTheCosting;

        return $this;
    }

    public function getWeekNumber(): ?int
    {
        return $this->weekNumber;
    }

    public function setWeekNumber(?int $weekNumber): self
    {
        $this->weekNumber = $weekNumber;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
