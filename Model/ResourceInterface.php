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

use AT\ResourceAccessBundle\Entity\Resource;

interface ResourceInterface
{
    /**
     * Returns the associated resource
     *
     * @return Resource
     */
    public function getResource();
}