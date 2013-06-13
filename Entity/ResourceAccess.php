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
use AT\ResourceAccessBundle\Entity\Resource;
use AT\ResourceAccessBundle\Entity\Requester;
use AT\ResourceAccessBundle\Model\ResourceInterface;
use AT\ResourceAccessBundle\Model\RequesterInterface;

/**
 * @ORM\Entity(repositoryClass="AT\ResourceAccessBundle\Repository\ResourceAccessRepository")
 * @ORM\Table(name="at_resource_accesses")
 */
class ResourceAccess
{
    const ACCESS_SUPER_ADMIN = 1;
    const ACCESS_ADMIN_1 = 11;
    const ACCESS_ADMIN_2 = 12;
    const ACCESS_MODERATOR_1 = 111;
    const ACCESS_MODERATOR_2 = 121;
    const ACCESS_REVIEWER_2 = 122;
    const ACCESS_EDIT_1 = 1111;
    const ACCESS_READ_1 = 1112;
    const ACCESS_EDIT_2 = 1211;
    const ACCESS_READ_2 = 1212;
    const ACCESS_EDIT_REVIEW = 1221;
    const ACCESS_READ_REVIEW = 1222;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="resourceAccesses", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id", nullable=false)
     */
    protected $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Requester", inversedBy="resourceAccesses")
     * @ORM\JoinColumn(name="requester_id", referencedColumnName="id", nullable=false)
     */
    protected $requester;

    /**
     * @ORM\Column(name="access_level", type="array")
     */
    protected $accessLevels;

    /**
     * @ORM\ManyToOne(targetEntity="Requester", inversedBy="grants")
     * @ORM\JoinColumn(name="granted_by", referencedColumnName="id")
     */
    protected $grantedBy;

    public function __construct()
    {
        $this->accessLevels = [];
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return $this
     */
    public function setResource(ResourceInterface $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param RequesterInterface $requester
     *
     * @return $this
     */
    public function setRequester(RequesterInterface $requester)
    {
        $this->requester = $requester;

        return $this;
    }

    /**
     * @return Requester
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public function setAccessLevels($array)
    {
        $this->accessLevels = $array;

        return $this;
    }

    /**
     * @param array $accessLevels
     *
     * @return $this
     */
    public function addAccessLevels($accessLevels)
    {
        $newRoles = array_diff($accessLevels, $this->accessLevels);
        if (!empty($newRoles)) {
            $this->accessLevels = array_merge($this->accessLevels, $newRoles);
        }

        return $this;
    }

    /**
     * @param array $accessLevels
     *
     * @return $this
     */
    public function removeAccessLevels($accessLevels)
    {
        $remainingRoles = array_diff($this->accessLevels, $accessLevels);

        $this->accessLevels = $remainingRoles;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAccessLevels()
    {
        return $this->accessLevels;
    }

    /**
     * @param RequesterInterface $requester
     *
     * @return $this
     */
    public function setGrantedBy(RequesterInterface $requester)
    {
        $this->grantedBy = $requester;

        return $this;
    }

    /**
     * @return RequesterInterface
     */
    public function getGrantedBy()
    {
        return $this->grantedBy;
    }
}