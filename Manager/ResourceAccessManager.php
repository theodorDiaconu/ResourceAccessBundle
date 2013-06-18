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

namespace AT\ResourceAccessBundle\Manager;

use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use AT\ResourceAccessBundle\Model\RoleHierarchyBuilder;
use AT\ResourceAccessBundle\Repository\ResourceAccessRepository;
use AT\ResourceAccessBundle\Model\RequesterInterface;
use AT\ResourceAccessBundle\Model\ResourceInterface;
use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Model\RoleHierarchyContainer;

class ResourceAccessManager
{
    /** @var EntityManager */
    private $entityManager;
    /** @var ResourceAccessRepository */
    private $repository;
    /** @var SecurityContextInterface */
    private $securityContext;
    /** @var RoleHierarchyContainer */
    private $roleHierarchyContainer;

    public function __construct(EntityManager $entityManager, SecurityContextInterface $securityContext, $roleHierarchies, $cacheDir)
    {
        $this->entityManager   = $entityManager;
        $this->repository      = $entityManager->getRepository("ATResourceAccessBundle:ResourceAccess");
        $this->securityContext = $securityContext;

        $this->load($cacheDir, $roleHierarchies);
    }

    /**
     * This checks if the roleHierarchyContainer exists in the cache.
     * If not we will create it.
     *
     * The $forceLoad parameter is used for tests so we can override the roles from the config.yml file
     *
     * @param $cacheDir
     * @param $roleHierarchies
     * @param bool $forceLoad
     */
    public function load($cacheDir, $roleHierarchies, $forceLoad = false)
    {
        $cachePath                    = $cacheDir . RoleHierarchyContainer::CACHE_PATH;
        $cachedRoleHierarchyContainer = unserialize(file_get_contents($cachePath));

        if (!$forceLoad && $cachedRoleHierarchyContainer instanceof RoleHierarchyContainer) {
            $this->roleHierarchyContainer = $cachedRoleHierarchyContainer;
        } else {
            $this->roleHierarchyContainer = new RoleHierarchyContainer($roleHierarchies);
        }
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
     * @param $accessLevel
     * @param ResourceInterface $customResource
     * @param RequesterInterface $requester
     *
     * @return bool
     * @throws \Symfony\Component\Validator\Exception\InvalidArgumentException
     */
    public function isGranted($accessLevel, ResourceInterface $customResource, RequesterInterface $requester = null)
    {
        if (null === $requester) {
            $token = $this->securityContext->getToken();

            if ($token instanceof AnonymousToken || null === $token) {
                throw(new InvalidArgumentException('Invalid or no requester provided.'));
            }

            $requester = $token->getUser();
        }

        /** @var ResourceInterface $resource */
        $resource = $customResource->getResource();
        $accesses = $this->repository->getAccessLevels($requester, $resource);

        if (null == $accesses) {
            return false;
        }

        $roleHierarchy = $this->roleHierarchyContainer->get(get_class($customResource));

        foreach ($accesses as $access) {
            $hasAccess = $roleHierarchy->isRoleParentOf($access, $accessLevel);
            if ($hasAccess) {
                return true;
            }
        }

        return false;
    }

    /**
     * Grants access to User for Resource
     *
     * @param RequesterInterface $requester
     * @param ResourceInterface $customResource
     * @param array $accessLevels
     * @param RequesterInterface $grantedBy
     *
     * @throws \Symfony\Component\Validator\Exception\InvalidArgumentException
     */
    public function grantAccess(RequesterInterface $requester, ResourceInterface $customResource, $accessLevels = [], RequesterInterface $grantedBy = null)
    {
        /** @var ResourceInterface $resource */
        $resource        = $customResource->getResource();
        $roleHierarchy   = $this->roleHierarchyContainer->get(get_class($customResource));
        $superAdminValue = $roleHierarchy->getRootValue();

        if (null !== $grantedBy && !$this->isGranted($superAdminValue, $resource, $grantedBy)) {
            throw(new InvalidArgumentException('The user with id ' . $grantedBy->getId() . ' is not allowed to grant access'));
        }

        /** @var ResourceAccess $resourceAccess */
        $resourceAccess = $this->repository->findOneBy(['requester' => $requester, 'resource' => $resource]);

        if (null === $resourceAccess) {
            $resourceAccess = new ResourceAccess();
            $resourceAccess
                ->setRequester($requester)
                ->setResource($resource)
                ->setAccessLevels($accessLevels);

            if (null !== $grantedBy) {
                $resourceAccess->setGrantedBy($grantedBy);
            }

            $this->save($resourceAccess);
        } else {
            $resourceAccess->addAccessLevels($accessLevels);

            $this->update($resourceAccess);
        }
    }

    /**
     * Resets access for given Resource to the provided access levels
     *
     * @param RequesterInterface $requester
     * @param ResourceInterface $customResource
     * @param array $accessLevels
     * @param RequesterInterface $grantedBy
     *
     * @throws \Exception
     */
    public function updateAccessLevels(RequesterInterface $requester, ResourceInterface $customResource, $accessLevels = [], RequesterInterface $grantedBy = null)
    {
        $resource       = $customResource->getResource();
        /** @var ResourceAccess $resourceAccess */
        $resourceAccess = $this->repository->findOneBy(['requester' => $requester, 'resource' => $resource]);

        if (null === $resourceAccess) {
            throw(new InvalidArgumentException('The user with id ' . $requester->getId() . ' has no access. Please use the method grantAccess'));
        }

        $resourceAccess->setAccessLevels($accessLevels);

        if (null !== $grantedBy) {
            $resourceAccess->setGrantedBy($grantedBy);
        }

        $this->update($resourceAccess);
    }

    /**
     * Removes access levels for specified Resource
     *
     * @param RequesterInterface $requester
     * @param ResourceInterface $customResource
     * @param array $accessLevels
     *
     * @throws \Exception
     */
    public function removeAccessLevels(RequesterInterface $requester, ResourceInterface $customResource, $accessLevels = [])
    {
        $resource       = $customResource->getResource();
        /** @var ResourceAccess $resourceAccess */
        $resourceAccess = $this->repository->findOneBy(['requester' => $requester, 'resource' => $resource]);

        if (null === $resourceAccess) {
            throw(new InvalidArgumentException('The user with id ' . $requester->getId() . ' already has no access.'));
        }

        $resourceAccess->removeAccessLevels($accessLevels);
        $this->update($resourceAccess);
    }

    /**
     * Removes any access for given User
     *
     * @param RequesterInterface $requester
     * @param ResourceInterface $resource
     *
     * @throws \Symfony\Component\Validator\Exception\InvalidArgumentException
     */
    public function removeAccess(RequesterInterface $requester, ResourceInterface $resource)
    {
        /** @var ResourceAccess $resourceAccess */
        $resourceAccess = $this->repository->findOneBy(['requester' => $requester, 'resource' => $resource]);

        if (null === $resourceAccess) {
            throw(new InvalidArgumentException('The user with id ' . $requester->getId() . ' already has no access.'));
        }

        $this->delete($resourceAccess);
    }
}