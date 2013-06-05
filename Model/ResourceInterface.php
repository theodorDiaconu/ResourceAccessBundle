<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
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