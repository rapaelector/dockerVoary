<?php

namespace App\Entity;

use App\Entity\Common\BlameableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Repository\ProjectMetaRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @ORM\Entity(repositoryClass=ProjectMetaRepository::class)
 */
class ProjectMeta
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $reasonLost;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"data-project"})
     */
    private $otherText;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReasonLost(): ?string
    {
        return $this->reasonLost;
    }

    public function setReasonLost(?string $reasonLost): self
    {
        $this->reasonLost = $reasonLost;

        return $this;
    }

    public function getOtherText(): ?string
    {
        return $this->otherText;
    }

    public function setOtherText(?string $otherText): self
    {
        $this->otherText = $otherText;

        return $this;
    }
}
