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

class RoleHierarchyContainer
{
    const CACHE_DIR  = '/at_resource_access';
    const CACHE_PATH = '/at_resource_access/RoleHierarchies.php';

    /** @var array $_elements */
    private $_elements;

    /**
     * @param array $resources
     */
    public function __construct($resources)
    {
        $this->process($resources);
    }

    /**
     * Builds the role hierarchy for each class defined under at_resource_access -> resources in config.yml
     *
     * @param array $resources
     */
    private function process($resources)
    {
        foreach ($resources as $resourceClass => $config) {
            $this->_elements[$resourceClass] = RoleHierarchyBuilder::build($config['role_hierarchy']);
        }
    }

    /**
     * Returns the role hierarchy for specified class
     *
     * @param string $className
     *
     * @return RoleHierarchy
     */
    public function get($className)
    {
        if (substr($className, 0, 14) == 'Proxies\__CG__') {
            $className = substr($className, 15);
        }

        return $this->_elements[$className];
    }
}