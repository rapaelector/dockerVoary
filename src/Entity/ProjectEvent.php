<?php

namespace App\Entity;

use App\Repository\ProjectEventRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Entity\Traits\ProjectEventTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=ProjectEventRepository::class)
 */
class ProjectEvent
{
    use BlameableTrait;
    use SoftDeleteableTrait;
    use TimestampableTrait;
    use ProjectEventTrait;

    // EVENT TYPE USE IN PROJECT SCHEDULER
    const EVENT_TYPE_FRAME_ASSEMBLY = 'frame_assembly'; // montage charpente
    const EVENT_TYPE_ISOTHERMAL_PANELS = 'isothermal_panels'; // panneaux isothermes
    const EVENT_TYPE_ENVELOPE = 'envelope'; // enveloppe
    const EVENT_TYPE_FORECASTS = 'forecasts'; // prévisions
    const EVENT_TYPE_SUBCONTRACTING = 'subcontracting'; // sous traitance
    const EVENT_TYPE_SHADE_HOUSE = 'shade_house'; // ombrière

    const PROJECT_EVENT_TYPES = [
        self::EVENT_TYPE_FRAME_ASSEMBLY,
        self::EVENT_TYPE_ISOTHERMAL_PANELS,
        self::EVENT_TYPE_ENVELOPE,
        self::EVENT_TYPE_FORECASTS,
        self::EVENT_TYPE_SUBCONTRACTING,
        self::EVENT_TYPE_SHADE_HOUSE,
    ];
    
    const PAYMENT_COLOR = '#000';
    const DEFAULT_EVENT_BACKGROUND = '#fff';
    const PAYMENT_BACKGROUND_COLOR = 'transparent';
    const EVENT_TYPE_SHADE_HOUSE_COLOR = '#fff';
    
    const DARK_COLOR = '#000';
    const LIGHT_COLOR = '#fff';

    const EVENT_NEW_COLORS = [
        self::EVENT_TYPE_FRAME_ASSEMBLY => ['label' => 'frame_assembly', 'backColor' => '#00b050', 'fontColor' => self::DARK_COLOR],
        self::EVENT_TYPE_ISOTHERMAL_PANELS => ['label' => 'isothermal_panels', 'backColor' => '#f79646', 'fontColor' => self::DARK_COLOR],
        self::EVENT_TYPE_ENVELOPE => ['label' => 'envelope', 'backColor' => '#ffc000', 'fontColor' => self::DARK_COLOR],
        self::EVENT_TYPE_FORECASTS => ['label' => 'forecasts', 'backColor' => '#c5d9f1', 'fontColor' => self::DARK_COLOR],
        self::EVENT_TYPE_SUBCONTRACTING => ['label' => 'subcontracting', 'backColor' => '#da9694', 'fontColor' => self::DARK_COLOR],
        // self::EVENT_TYPE_SHADE_HOUSE => ['label' => 'shade_house', 'backColor' => '#1f497d', 'fontColor' => self::LIGHT_COLOR],
        self::EVENT_TYPE_SHADE_HOUSE => ['label' => 'shade_house', 'backColor' => '#1f497d', 'fontColor' => self::DARK_COLOR],
    ];
    
    ////////////////////// END OF EVENT TYPE //////////////////

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"projectEvent:scheduler"})
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"data-project", "projectEvent:scheduler"})
     */
    private $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"data-project", "projectEvent:scheduler"})
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project", "projectEvent:scheduler"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="events", cascade={"all"})
     * @Groups({"projectEvent:scheduler"})
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
