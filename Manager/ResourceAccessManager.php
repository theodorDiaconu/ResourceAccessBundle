<?php

namespace AT\ResourceAccessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Exception\Exception;
use AT\ResourceAccessBundle\Repository\ResourceAccessRepository;
use AT\ResourceAccessBundle\Model\RequesterInterface;
use AT\ResourceAccessBundle\Model\ResourceInterface;
use AT\ResourceAccessBundle\Entity\ResourceAccess;

class ResourceAccessManager
{
    /** @var EntityManager */
    private $entityManager;
    /** @var ResourceAccessRepository */
    private $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository("ATResourceAccessBundle:ResourceAccess");
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    /**
     * @param ResourceAccess $resourceAccess
     */
    public function save(ResourceAccess $resourceAccess)
    {
        $this->entityManager->persist($resourceAccess);
        $this->flush();
    }

    /**
     * @param ResourceAccess $resourceAccess
     */
    public function delete(ResourceAccess $resourceAccess)
    {
        $this->entityManager->remove($resourceAccess);
        $this->flush();
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public function findBy($arr)
    {
        return $this->repository->findBy($arr);
    }

    /**
     * @param $arr
     *
     * @return null|ResourceAccess
     */
    public function findOneBy($arr)
    {
        return $this->repository->findOneBy($arr);
    }

    /**
     * @param ResourceAccess $resourceAccess
     */
    public function update(ResourceAccess $resourceAccess)
    {
        $this->save($resourceAccess);
    }

    /**
     * Checks if User has specified access level for resource
     *
     * @param RequesterInterface $requester
     * @param ResourceInterface $resource
     * @param integer $accessLevel
     *
     * @return boolean
     * @throws \Exception
     */
    public function isGranted(RequesterInterface $requester, ResourceInterface $resource, $accessLevel)
    {
        if (!in_array($accessLevel, ResourceAccess::$validAccesses)) {
            throw(new \Exception("Invalid access level " . $accessLevel));
        }

        $access = $this->repository->getAccessLevel($requester, $resource);

        if (null === $access) {
            return false;
        } elseif ($access == CampaignAccess::ACCESS_ADMIN) {
            return true;
        } elseif ($access == CampaignAccess::ACCESS_EDIT && $accessLevel >= CampaignAccess::ACCESS_EDIT ) {
            return true;
        } elseif ($access == CampaignAccess::ACCESS_READ && $accessLevel === CampaignAccess::ACCESS_READ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Grants access to User for Resource
     *
     * @param RequesterInterface $requester
     * @param ResourceInterface $customResource
     * @param integer $accessLevel
     * @param RequesterInterface $grantedBy
     *
     * @throws \Exception
     */
    public function grantAccess(RequesterInterface $requester, ResourceInterface $customResource, $accessLevel, RequesterInterface $grantedBy = null)
    {
        $resource = $customResource->getResource();

        if (null !== $grantedBy && !$this->isGranted($grantedBy, $resource, CampaignAccess::ACCESS_ADMIN)) {
            throw(new \Exception('The user with id ' . $requester->getId() . ' is not allowed to grant access'));
        }

        /** @var CampaignAccess $campaignAccess */
        $resourceAccess = $this->repository->findOneBy(['requester' => $requester, 'resource' => $resource]);

        if (null === $resourceAccess) {
            $resourceAccess = new ResourceAccess();
            $resourceAccess
                ->setRequester($requester)
                ->setResource($resource)
                ->setGrantedBy($grantedBy)
                ->setAccessLevel($accessLevel)
            ;

            $this->entityManager->persist($resourceAccess);
            $this->entityManager->flush();
        }
    }

    /**
     * Updates access for given ResourceAccess
     *
     * @param ResourceAccess $resourceAccess
     * @param $accessLevel
     * @param RequesterInterface $grantedBy
     */
    public function updateAccess(ResourceAccess $resourceAccess, $accessLevel, RequesterInterface $grantedBy = null)
    {
        $resourceAccess->setAccessLevel($accessLevel);
        $resourceAccess->setGrantedBy($grantedBy);
        $this->update($resourceAccess);
    }

    /**
     * Removes access for given User
     *
     * @param RequesterInterface $requester
     * @param ResourceInterface $resource
     */
    public function removeAccess(RequesterInterface $requester, ResourceInterface $resource)
    {
        /** @var ResourceAccess $resourceAccess */
        $resourceAccess = $this->repository->findOneBy(['requester' => $requester, 'resource' => $resource]);

        $this->delete($resourceAccess);
    }
}