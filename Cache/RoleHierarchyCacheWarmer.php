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

namespace AT\ResourceAccessBundle\Cache;

use AT\ResourceAccessBundle\Model\RoleHierarchyContainer;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

class RoleHierarchyCacheWarmer extends CacheWarmer
{
    private $roleHierarchies;

    /**
     * @param $roleHierarchies
     */
    public function __construct($roleHierarchies)
    {
        $this->roleHierarchies = $roleHierarchies;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $roleHierarchyContainer = new RoleHierarchyContainer($this->roleHierarchies);
        $serialized             = serialize($roleHierarchyContainer);
        $cachePath              = $cacheDir . RoleHierarchyContainer::CACHE_DIR;

        if (!file_exists($cachePath)) {
            mkdir($cachePath);
        }

        $this->writeCacheFile($cacheDir . RoleHierarchyContainer::CACHE_PATH, $serialized);
    }

    /**
     * @return boolean
     */
    public function isOptional()
    {
        return false;
    }
}