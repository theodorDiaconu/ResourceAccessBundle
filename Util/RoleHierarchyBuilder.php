<?php

namespace AT\ResourceAccessBundle\Util;

use AT\ResourceAccessBundle\Model\RoleHierarchy;

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

        $root = new RoleNode($rootValue);
        $children = [];
        static::roleIterator($array[$rootValue], $root, $children);

        $roleHierarchy = new RoleHierarchy($root, $children);

        return $roleHierarchy;
    }

    protected static function roleIterator($arr, $parent, &$children)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $roleNode = new RoleNode($key, $parent);
                $children[] = $roleNode;
                static::roleIterator($value, $roleNode, $children);
            } else {
                $finalNode = new RoleNode($value ,$parent);
                $children[] = $finalNode;
            }
        }
    }
}