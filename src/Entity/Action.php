<?php

namespace App\Entity;

use App\Entity\Common\BlameableTrait;
use App\Entity\Common\TimestampableTrait;
use App\Entity\Common\SoftDeleteableTrait;
use App\Repository\ActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @ORM\Entity(repositoryClass=ActionRepository::class)
 */
class Action
{
    use SoftDeleteableTrait;
    use BlameableTrait;
    use TimestampableTrait;

    const ACTION_CHANGE_STATUS = 'change_status';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $previousValue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPreviousValue(): ?string
    {
        return $this->previousValue;
    }

    public function setPreviousValue(?string $previousValue): self
    {
        $this->previousValue = $previousValue;

        return $this;
    }
}
