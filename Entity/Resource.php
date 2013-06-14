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

namespace AT\ResourceAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Model\ResourceInterface;
use AT\ResourceAccessBundle\Tests\Model\Roles;

/**
 * @ORM\Entity
 * @ORM\Table(name="at_resources")
 */
class Resource implements ResourceInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="ResourceAccess", mappedBy="resource", cascade={"persist", "remove"})
     */
    protected $resourceAccesses;

    public function __construct()
    {
        $this->resourceAccesses = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ResourceAccess $resourceAccess
     *
     * @return $this
     */
    public function addResourceAccess(ResourceAccess $resourceAccess)
    {
        $this->resourceAccesses->add($resourceAccess);
        $resourceAccess->setResource($this);

        return $this;
    }

    /**
     * @param ResourceAccess $resourceAccess
     *
     * @return $this
     */
    public function removeResourceAccess(ResourceAccess $resourceAccess)
    {
        $this->resourceAccesses->removeElement($resourceAccess);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getResourceAccesses()
    {
        return $this->resourceAccesses;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getRoleHierarchy()
    {
        $roleHierarchy = [
            Roles::ACCESS_SUPER_ADMIN => [
                Roles::ACCESS_ADMIN_1 => [
                    Roles::ACCESS_MODERATOR_1 => [
                        Roles::ACCESS_EDIT_1 => [
                            Roles::ACCESS_READ_1
                        ]
                    ]
                ],
                Roles::ACCESS_ADMIN_2 => [
                    Roles::ACCESS_MODERATOR_2 => [
                        Roles::ACCESS_EDIT_2 => [
                            Roles::ACCESS_READ_2
                        ]
                    ],
                    Roles::ACCESS_REVIEWER_2  => [
                        Roles::ACCESS_EDIT_REVIEW,
                        Roles::ACCESS_READ_REVIEW
                    ]
                ]
            ]
        ];

        return $roleHierarchy;
    }
}