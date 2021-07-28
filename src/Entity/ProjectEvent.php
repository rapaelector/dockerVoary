<?php

namespace App\Entity;

use App\Repository\ProjectEventRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass=ProjectEventRepository::class)
 */
class ProjectEvent
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
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"data-project"})
     */
    private $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"data-project"})
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="events", cascade={"all"})
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
