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

namespace AT\ResourceAccessBundle\Util;

use AT\ResourceAccessBundle\Model\RoleHierarchy;
use AT\ResourceAccessBundle\Util\RoleNode;

class RoleHierarchyBuilder
{
    /**
     * @param $array
     *
     * @return RoleHierarchy
     */
    static function build($array)
    {
        reset($array);
        $rootValue = key($array);

        $root     = new RoleNode($rootValue);
        $children = [];
        static::roleIterator($array[$rootValue], $root, $children);

        $roleHierarchy = new RoleHierarchy($root, $children);

        return $roleHierarchy;
    }

    /**
     * @param array $arr
     * @param RoleNode|null $parent
     * @param array $children
     */
    protected static function roleIterator($arr, $parent, &$children)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $roleNode   = new RoleNode($key, $parent);
                $children[] = $roleNode;
                static::roleIterator($value, $roleNode, $children);
            } else {
                $finalNode  = new RoleNode($value, $parent);
                $children[] = $finalNode;
            }
        }
    }
}