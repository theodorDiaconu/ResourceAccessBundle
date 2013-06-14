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

use AT\ResourceAccessBundle\Util\RoleNode;

class RoleHierarchy
{
    /** @var RoleNode $root */
    protected $root;
    /** @var RoleNode[] */
    protected $children;

    /**
     * @param RoleNode $root
     * @param array $children
     */
    public function __construct(RoleNode $root, $children)
    {
        $this->root     = $root;
        $this->children = $children;
    }

    public function isRoleParentOf($role, $target)
    {
        if ($target == $role) {
            return true;
        }

        if ($this->root->getValue() == $role) {
            return true;
        }

        if ($target == $this->root->getValue()) {
            return false;
        }

        foreach ($this->children as $child) {
            if ($child->getValue() == $target) {
                //we check the next parent maybe it's close :)
                if ($child->getParent()->getValue() == $role) {
                    return true;
                } else {
                    // we go to the next parent
                    $nextChild = $child->getParent();
                }

                while (true) {
                    if ($nextChild->getParent() === null) {
                        // we hit the end
                        return false;
                    } else {
                        if ($nextChild->getParent()->getValue() == $role) {
                            //we found it
                            return true;
                        } else {
                            $nextChild = $nextChild->getParent();
                        }
                    }
                }
            }
        }
    }
}