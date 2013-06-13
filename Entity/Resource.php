<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
 */

namespace AT\ResourceAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Model\ResourceInterface;

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
            ResourceAccess::ACCESS_SUPER_ADMIN => [
                ResourceAccess::ACCESS_ADMIN_1 => [
                    ResourceAccess::ACCESS_MODERATOR_1 => [
                        ResourceAccess::ACCESS_EDIT_1 => [
                            ResourceAccess::ACCESS_READ_1
                        ]
                    ]
                ],
                ResourceAccess::ACCESS_ADMIN_2 => [
                    ResourceAccess::ACCESS_MODERATOR_2 => [
                        ResourceAccess::ACCESS_EDIT_2 => [
                            ResourceAccess::ACCESS_READ_2
                        ]
                    ],
                    ResourceAccess::ACCESS_REVIEWER_2 => [
                        ResourceAccess::ACCESS_EDIT_REVIEW,
                        ResourceAccess::ACCESS_READ_REVIEW
                    ]
                ]
            ]
        ];

        return $roleHierarchy;
    }
}