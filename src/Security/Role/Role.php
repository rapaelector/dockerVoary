<?php

/**
 * @Author: Stephan<srandriamahenina@bocasay.com>
 * @Date:   2017-07-28 09:55:17
 * @Last Modified by:   Stephan
 * @Last Modified time: 2017-11-21 14:42:45
 */

namespace App\Security\Role;

class Role
{
    private $name;

    private $role;

    private $action;

    private $children;

    private $parent;

    public function __construct()
    {
        $this->children = [];
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function addChild(Role $role)
    {
        $this->children[] = $role;

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setParent(Role $role)
    {
        $this->parent = $role;
        $role->addChild($this);

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function __toString()
    {
        return $this->name . " - " . $this->role;
    }

    public function getActionRole($action)
    {
        if ($this->action == $action) return $this;
        if (count($this->children) > 0) {
            foreach ($this->children as $role) {
                if ($role->getAction() == $action) {
                    return $role;
                }
            }
        }

        return null;
    }

    public function isSingle()
    {
        return is_null($this->parent) && count($this->children) == 0;
    }
}
