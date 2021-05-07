<?php

namespace App\Entity\Common;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait BlameableTrait
{
	/**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"blameable"})
     */
    private $createdBy;
    
    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"blameable"})
     */
    private $updatedBy;	

    /**
     * Get $createdBy
     *
     * @return  User
     */ 
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set $createdBy
     *
     * @param  User  $createdBy  $createdBy
     *
     * @return  self
     */ 
    public function setCreatedBy(?User $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get $updatedBy
     *
     * @return  User
     */ 
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set $updatedBy
     *
     * @param  User  $updatedBy  $updatedBy
     *
     * @return  self
     */ 
    public function setUpdatedBy(?User $updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
