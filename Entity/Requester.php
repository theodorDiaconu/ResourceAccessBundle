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
use AT\ResourceAccessBundle\Model\RequesterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="at_requesters")
 */
class Requester implements RequesterInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="ResourceAccess", mappedBy="requester", cascade={"persist", "remove"})
     */
    protected $resourceAccesses;

    /**
     * @ORM\OneToMany(targetEntity="ResourceAccess", mappedBy="grantedBy", cascade={"persist", "remove"})
     */
    protected $grants;

    public function __construct()
    {
        $this->resourceAccesses = new ArrayCollection();
        $this->grants = new ArrayCollection();
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
        $resourceAccess->setRequester($this);

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
     * @param ResourceAccess $resourceAccess
     *
     * @return $this
     */
    public function addGrant(ResourceAccess $resourceAccess)
    {
        $this->grants->add($resourceAccess);
        $resourceAccess->setGrantedBy($this);

        return $this;
    }

    /**
     * @param ResourceAccess $resourceAccess
     *
     * @return $this
     */
    public function removeGrant(ResourceAccess $resourceAccess)
    {
        $this->resourceAccesses->removeElement($resourceAccess);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGrants()
    {
        return $this->grants;
    }

    public function __toString()
    {
        return 'test_requester';
    }
}