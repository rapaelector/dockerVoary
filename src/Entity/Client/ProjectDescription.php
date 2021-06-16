<?php

namespace App\Entity\Client;

use App\Repository\Client\ProjectDescriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectDescriptionRepository::class)
 */
class ProjectDescription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Description du projet
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    private $projectDescription;

    /**
     * Surface
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $area;

    /**
     * Type de marche
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $marketType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectDescription(): ?string
    {
        return $this->projectDescription;
    }

    public function setProjectDescription(?string $projectDescription): self
    {
        $this->projectDescription = $projectDescription;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getMarketType(): ?string
    {
        return $this->marketType;
    }

    public function setMarketType(?string $marketType): self
    {
        $this->marketType = $marketType;

        return $this;
    }
}
