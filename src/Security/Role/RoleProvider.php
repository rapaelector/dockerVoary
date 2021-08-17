<?php

/**
 * @Author: Stephan<srandriamahenina@bocasay.com>
 * @Date:   2017-07-28 09:59:12
 * @Last Modified by:   stephan <m6ahenina@gmail.com>
 * @Last Modified time: 2019-11-15 13:31:16
 */

namespace App\Security\Role;

class RoleProvider
{
    const ACTION_ADD = "role.action.add";
    const ACTION_EDIT = "role.action.edit";
    const ACTION_VIEW = "role.action.view";
    const ACTION_DELETE = "role.action.delete";
    // Soumettre
    const ACTION_SUBMIT = "role.action.submit";
    const ACTION_SEND = "role.action.send";
    const ACTION_ALL = "role.action.all";
    const ACTION_ARCHIVE = "role.action.archive";
    const ACTION_PUBLISH = "role.action.publish";
    const ACTION_VALIDATE = "role.action.validate";
    const ACTION_BILLABLE = "role.action.billable";

    public static function getActions()
    {
        $actions = [
            self::ACTION_ALL,
            self::ACTION_VIEW,
            self::ACTION_ADD,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
            self::ACTION_VALIDATE,
        ];

        return $actions;
    }

    public static function loadRoles()
    {
        $rolesArray = self::getRolesArray();
        $roles = self::createRoles($rolesArray);

        return $roles;
    }

    public static function getRawRoles()
    {
        $roles = self::loadRoles();
        $raw = [];

        foreach ($roles as $role) {
            $raw = array_merge($raw, self::extractRoles($role));
        }

        return $raw;
    }

    public static function getParentRoles()
    {
        $roles = self::loadRoles();
        $parents = [];

        foreach ($roles as $role) {
            $parents[] = $role->getRole();
        }

        return $parents;
    }

    public static function extractRoles(Role $role)
    {
        $raw = [$role->getRole()];

        if (count($role->getChildren()) > 0) {
            foreach ($role->getChildren() as $child) {
                $raw = array_merge($raw, self::extractRoles($child));
            }
        }

        return $raw;
    }

    public static function createRoles(array $rolesArray, Role $parent = null)
    {
        $roles = [];

        foreach ($rolesArray as $roleArray) {
            $role = new Role();
            if (isset($roleArray['name'])) {
                $role->setName($roleArray['name']);
            }
            $role->setRole($roleArray['role']);
            $role->setAction($roleArray['action']);
            if (!is_null($parent)) {
                $role->setParent($parent);
            }

            if (isset($roleArray['children'])) {
                self::createRoles($roleArray['children'], $role);
            }

            $roles[] = $role;
        }

        return $roles;
    }

    public static function getRolesArray()
    {
        $roles = [];
        $roles[] = self::getUserRoles();
        $roles[] = self::getClientRoles();
        $roles[] = self::getProjectRoles();
        $roles[] = self::getLoadPlanRoles();
        $roles[] = self::getProjectSchedulerRoles();

        return $roles;
    }

    public static function getUserRoles()
    {
        return self::buildRoles('user.role', 'ROLE_USER', [
            self::ACTION_VIEW,
            self::ACTION_ADD,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
        ]);
    }

    public static function getClientRoles()
    {
        return self::buildRoles('prospect.role', 'ROLE_CLIENT', [
            self::ACTION_VIEW,
            self::ACTION_ADD,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
        ]);
    }

    public static function getProjectRoles()
    {
        return self::buildRoles('project.role', 'ROLE_PROJECT', [
            self::ACTION_VIEW,
            self::ACTION_ADD,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
            // self::ACTION_SUBMIT,
            // self::ACTION_ARCHIVE,
            self::ACTION_VALIDATE,
        ]);
    }

    public static function getLoadPlanRoles()
    {
        return self::buildRoles('load_plan.role', 'ROLE_LOAD_PLAN', [
            self::ACTION_VIEW,
            self::ACTION_ADD,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
        ]);
    }

    public static function getProjectSchedulerRoles()
    {
        return self::buildRoles('project_scheduler.role', 'ROLE_POJECT_SCHEDULER', [
            self::ACTION_VIEW,
        ]);
    }

    public static function buildRoleFromAction($parent_role, $action)
    {
        return $parent_role . '_'. strtoupper(explode('.', $action)[2]);
    }

    public static function buildRoles($name, $role, array $actions = [])
    {
        $children = [];
        foreach ($actions as $action) {
            $children[] = ['role' => self::buildRoleFromAction($role, $action), 'action' => $action];
        }

        return [
            'name' => $name,
            'role' => $role,
            'action' => self::ACTION_ALL,
            'children' => $children,
        ];
    }
}
