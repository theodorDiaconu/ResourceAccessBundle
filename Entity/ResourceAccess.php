<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
 */

namespace AT\ResourceAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AT\ResourceAccessBundle\Entity\Resource;
use AT\ResourceAccessBundle\Entity\Requester;
use AT\ResourceAccessBundle\Model\RequesterInterface;

/**
 * @ORM\Entity(repositoryClass="AT\ResourceAccessBundle\Repository\ResourceAccessRepository")
 * @ORM\Table(name="resource_accesses")
 */
class ResourceAccess
{
    const ACCESS_ADMIN = 1;
    const ACCESS_EDIT = 2;
    const ACCESS_READ = 3;

    static $validAccesses = [ self::ACCESS_READ, self::ACCESS_EDIT, self::ACCESS_ADMIN ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="resourceAccesses")
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id", nullable=false)
     */
    protected $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Requester", inversedBy="resourceAccesses")
     * @ORM\JoinColumn(name="requester_id", referencedColumnName="id", nullable=false)
     */
    protected $requester;

    /**
     * @ORM\Column(name="access_level", type="integer")
     */
    protected $accessLevel;

    /**
     * @ORM\ManyToOne(targetEntity="Requester", inversedBy="grants")
     * @ORM\JoinColumn(name="granted_by", referencedColumnName="id")
     */
    protected $grantedBy;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Resource $resource
     *
     * @return $this
     */
    public function setResource(Resource $resource)
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
     * @param Requester $requester
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
     * @param integer $accessLevel
     *
     * @return $this
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

    /**
     * @param Requester $requester
     *
     * @return $this
     */
    public function setGrantedBy(RequesterInterface $requester)
    {
        $this->grantedBy = $requester;

        return $this;
    }

    /**
     * @return Requester
     */
    public function getGrantedBy()
    {
        return $this->grantedBy;
    }
}