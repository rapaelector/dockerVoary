<?php

namespace App\Entity\Client;

use App\Entity\Address;
use App\Entity\Traits\Client\ProjectDescriptionTrait;
use App\Repository\Client\ProjectDescriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProjectDescriptionRepository::class)
 */
class ProjectDescription
{
    use ProjectDescriptionTrait;

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
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $projectDescription;

    /**
     * Surface
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type("string")
     * @Groups({"project:scheduler-resource"})
     */
    private $area;

    /**
     * Type de marche
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type("string")
     * Assert\Choice(
     *      callback="checkMarketType",
     *      groups={"client:edit", "client:create"}
     * )
     */
    private $marketType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    private $department;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $address;

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

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }
}
