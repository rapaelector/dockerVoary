<?php

namespace App\Entity;

use App\Repository\LoadPlanRepository;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Entity\Traits\LoadPlanTrait;

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
    use LoadPlanTrait;
    
    /**
     * TASK TYPES
     * TYPE DE TACHE
    */
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
     * estimated_study_time, effective_study_time
     * Temps d'etude estime, temps d'etude effectif
     */
    const QUARTER_OF_A_DAY = 2; // 1/4 journée
    const HALF_DAY = 4; // 1/2 journée
    const ONE_DAY = 8; // 1 jour
    const ONE_DAY_AND_HALF = 12; // 1 jour et demi
    const TWO_DAYS = 16; // 2 jours
    const THREE_DAYS = 24; // 3 jours
    const FOUR_DAYS = 32; // 4 jours
    const FIVE_DAYS = 40; // 5 jours

    // Temps d'etude
    const STUDY_TIME = [
        self::QUARTER_OF_A_DAY,
        self::HALF_DAY,
        self::ONE_DAY,
        self::ONE_DAY_AND_HALF,
        self::TWO_DAYS,
        self::THREE_DAYS,
        self::FOUR_DAYS,
        self::FIVE_DAYS,
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
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="loadPlans")
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

    /**
     * Date butoire
     * 
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"loadPlan:list"})
     */
    private $deadline;

    /**
     * Date realisation devis
     * 
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"loadPlan:list"})
     */
    private $realizationQuotationDate;

    /**
     * Temps d'etude estimé
     * 
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"loadPlan:list"})
     * Assert\Choice(
     *      callback="getStudyTime",
     * )
     */
    private $effectiveStudyTime;

    /**
     * Temps d'etude effectif
     * 
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"loadPlan:list"})
     * Assert\Choice(
     *      callback="getStudyTime",
     * )
     */
    private $estimatedStudyTime;

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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getRealizationQuotationDate(): ?\DateTimeInterface
    {
        return $this->realizationQuotationDate;
    }

    public function setRealizationQuotationDate(?\DateTimeInterface $realizationQuotationDate): self
    {
        $this->realizationQuotationDate = $realizationQuotationDate;

        return $this;
    }

    public function getEffectiveStudyTime(): ?int
    {
        return $this->effectiveStudyTime;
    }

    public function setEffectiveStudyTime(?int $effectiveStudyTime): self
    {
        $this->effectiveStudyTime = $effectiveStudyTime;

        return $this;
    }

    public function getEstimatedStudyTime(): ?int
    {
        return $this->estimatedStudyTime;
    }

    public function setEstimatedStudyTime(?int $estimatedStudyTime): self
    {
        $this->estimatedStudyTime = $estimatedStudyTime;

        return $this;
    }
}