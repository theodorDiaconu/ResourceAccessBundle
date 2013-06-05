<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
 */

namespace AT\ResourceAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AT\ResourceAccessBundle\Entity\ResourceAccess;

/**
 * @ORM\Entity
 * @ORM\Table(name="resources")
 */
class Resource 
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
}