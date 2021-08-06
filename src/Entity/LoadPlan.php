<?php

namespace App\Entity;

use App\Repository\LoadPlanRepository;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
    
    // TASK TYPES // TYPE DE TACHE
    const METER_CONSULTATION = 'meter_consultation'; // Mètre Consultation 
    const PRE_STUDY = 'pre_study'; // Pré étude 
    const SKETCH = 'sketch'; // Esquisse 
    const ENCRYPTION = 'encryption'; // Chiffrage 

    const TASK_TYPES = [
        self::METER_CONSULTATION => self::METER_CONSULTATION,
        self::PRE_STUDY => self::PRE_STUDY,
        self::SKETCH => self::SKETCH,
        self::ENCRYPTION => self::ENCRYPTION,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"loadPlan:list"})
     */
    private $id;

    /**
     * Nature du chiffrage
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type("string")
     * @Groups({"loadPlan:list"})
     */
    private $natureOfTheCosting;

    /**
     * N° Semaine pour remise de l'etude
     * 
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("integer")
     * @Groups({"loadPlan:list"})
     */
    private $weekNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="loadPlans", cascade={"all"})
     * @Groups({"loadPlan:list"})
     * @Assert\NotBlank
     */
    private $project;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"loadPlan:list"})
     */
    private $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"loadPlan:list"})
     */
    private $end;

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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }
}
