<?php

/*
 * This file is part of the ResourceAccessBundle.
 *
 * (c) Theodor Diaconu <diaconu.theodor@gmail.com>
 * (c) Alexandru Miron <beliveyourdream@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AT\ResourceAccessBundle\Model;

use AT\ResourceAccessBundle\Model\RoleHierarchy;
use AT\ResourceAccessBundle\Model\RoleNode;

class RoleHierarchyBuilder
{
    /**
     * @param $roles
     *
     * @return RoleHierarchy
     */
    static function build($roles)
    {
        reset($roles);

        $rootValue = key($roles);
        $root      = new RoleNode($rootValue);
        $children  = [];

        static::roleIterator($roles, $root, $children);

        $roleHierarchy = new RoleHierarchy($root, $children);

        return $roleHierarchy;
    }

    /**
     * @param array $roles
     * @param RoleNode|null $root
     * @param array $children
     */
    protected static function roleIterator($roles, $root, &$children)
    {
        foreach ($roles as $value => $childrenValues) {
            if ($value !== $root->getValue()) {
                $isChild = static::isRoleInChildren($value, $children);
                if (false !== $isChild) {
                    $parent = $isChild;
                } else {
                    $parent = new RoleNode($value, $root);
                }
            } else {
                $parent = $root;
            }

            foreach ($childrenValues as $childValue) {
                $child      = new RoleNode($childValue, $parent);
                $children[] = $child;
            }
        }
    }

    /**
     * @param string $role
     * @param RoleNode[] $children
     *
     * @return boolean|RoleNode
     */
    protected static function isRoleInChildren($role, $children)
    {
        foreach ($children as $child) {
            if ($child->getValue() === $role) {
                return $child;
            }
        }

        return false;
    }
}