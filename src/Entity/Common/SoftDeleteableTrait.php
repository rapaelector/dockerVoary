<?php

/*
 * @Author: stephan <m6ahenina@gmail.com>
 * @Date: 2020-04-09 07:11:42
 * @Last Modified by: stephan <m6ahenina@gmail.com>
 * @Last Modified time: 2020-05-13 06:22:50
 */

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait SoftDeleteableTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"softdeleteable"})
     * 
     * @var \DateTimeInterface
     */
    private $deletedAt;

    /**
     * Get the value of deletedAt
     *
     * @return  \DateTimeInterface
     */ 
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set the value of deletedAt
     *
     * @param  \DateTimeInterface  $deletedAt
     *
     * @return  self
     */ 
    public function setDeletedAt(\DateTimeInterface $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
