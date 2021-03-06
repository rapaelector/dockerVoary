<?php

namespace App\Entity;

use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Repository\ExchangeHistoryRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ExchangeHistoryRepository::class)
 */
class ExchangeHistory
{
    use BlameableTrait;
    use SoftDeleteableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"exchange-history"})
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"exchange-history"})
     * @Assert\Date
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"exchange-history"})
     */
    private $description;

    /**
     * % DE CONFIANCE DANS LE PROJET
     * 
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(
     *  min = 0,
     *  max = 100,
     * )
     * @Groups({"exchange-history"})
     */
    private $percentage;

    /**
     * 
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="exchangeHistories")
     */
    private $Project;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"exchange-history"})
     */
    private $relaunchDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"exchange-history"})
     */
    protected $nextStepDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"exchange-history"})
     */
    private $flag;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"exchange-history"})
     */
    private $archiUser;

    /**
     * Prise de note
     * 
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"exchange-history"})
     */
    private $noteTaking;
    
    public function __construct()
    {
        $this->setNextStepDate(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPercentage(): ?string
    {
        return $this->percentage;
    }

    public function setPercentage(?string $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->Project;
    }

    public function setProject(?Project $Project): self
    {
        $this->Project = $Project;

        return $this;
    }

    public function getRelaunchDate(): ?\DateTimeInterface
    {
        return $this->relaunchDate;
    }

    public function setRelaunchDate(?\DateTimeInterface $relaunchDate): self
    {
        $this->relaunchDate = $relaunchDate;

        return $this;
    }

    public function getNextStepDate(): ?\DateTimeInterface
    {
        return $this->nextStepDate;
    }

    public function setNextStepDate(?\DateTimeInterface $nextStepDate): self
    {
        $this->nextStepDate = $nextStepDate;

        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getArchiUser(): ?string
    {
        return $this->archiUser;
    }

    public function setArchiUser(?string $archiUser): self
    {
        $this->archiUser = $archiUser;

        return $this;
    }

    public function getNoteTaking(): ?string
    {
        return $this->noteTaking;
    }

    public function setNoteTaking(?string $noteTaking): self
    {
        $this->noteTaking = $noteTaking;

        return $this;
    }
}
