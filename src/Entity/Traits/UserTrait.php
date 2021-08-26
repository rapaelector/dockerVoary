<?php

namespace App\Entity\Traits;

trait UserTrait
{
    /**
     * @Groups({"project-form-data", "data-project", "exchange-history", "loadPlan:planning", "project:scheduler-resource"})
     */
    public function getName(): ?string
    {
        $parts = [];
        if ($this->firstName) {
            $parts[] = $this->firstName;
        }
        if ($this->lastName) {
            $parts[] = $this->lastName;
        }

        return count($parts) > 0 ? implode(' ', $parts) : $this->email;
    }

    public function __toString()
    {
        return $this->getName() ? $this->getName() : '';
    }

    public function removeRole(string $role): self
    {
        if (in_array($role, $this->roles)) {
            $index = array_search($role, $this->roles);
            array_splice($this->roles, $index, 1);
        }

        return $this;
    }

    /**
     * This function add a role to the user's role
     * It checks if the role is not there yet, then add it.
     */
    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getFullNameWithEmail()
    {
        return $this->firstName . ' ' . $this->lastName . ' ('.$this->email.')';
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}