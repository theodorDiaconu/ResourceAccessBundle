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

class RoleNode
{
    protected $parent;
    protected $value;

    public function __construct($value, $parent = null)
    {
        $this->value  = $value;
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