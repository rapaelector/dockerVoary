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
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(
     *  min = 0,
     *  max = 100,
     * )
     */
    private $projectConfidencePercentage;

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

    public function getProjectConfidencePercentage(): ?string
    {
        return $this->projectConfidencePercentage;
    }

    public function setProjectConfidencePercentage(?string $projectConfidencePercentage): self
    {
        $this->projectConfidencePercentage = $projectConfidencePercentage;

        return $this;
    }
}
