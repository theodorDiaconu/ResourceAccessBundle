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

    /**
     * Returns an array of roles representing the role hierarchy tree
     *
     * Example:
     *
     * $roleHierarchy = [
     *      'ROLE_ADMIN' => [
     *          'ROLE_MODERATOR' => [
     *              'ROLE_MODERATOR_1',
     *              'ROLE_MODERATOR_2
     *          ],
     *          'ROLE_REVIEWER' => [
     *              'ROLE_REVIEWER_1' => [
     *                  'ROLE_REVIEWER_1_1',
     *                  'ROLE_REVIEWER_1_2'
     *              ],
     *              'ROLE_REVIEWER_2
     *          ]
     *      ]
     * ];
     *
     * @return array
     */
    public function getRoleHierarchy();
}