<?php

namespace AT\ResourceAccessBundle\Util;

class RoleNode 
{
    protected $parent;
    protected $value;

    public function __construct($value, $parent = null)
    {
        $this->value = $value;
        $this->parent = $parent;
    }

    /**
     * @return null|RoleNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}