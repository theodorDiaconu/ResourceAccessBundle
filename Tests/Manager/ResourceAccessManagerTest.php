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

namespace AT\ResourceAccessBundle\Tests\Manager;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AT\ResourceAccessBundle\Tests\TestBase;
use AT\ResourceAccessBundle\Tests\Entity\Requester;
use AT\ResourceAccessBundle\Entity\Resource;
use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Tests\Model\Roles;

class ResourceAccessManagerTest extends TestBase
{
    public function testIsGrantedWithInvalidUser()
    {
        $resource  = new Resource();
        $requester = new Requester();

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource);
    }

    public function testIsGranted()
    {
        $resource  = new Resource();
        $requester = new Requester();

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        // testing there is no access without a resourceAccess
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess = new ResourceAccess();
        $resourceAccess->setAccessLevels([Roles::ACCESS_SUPER_ADMIN]);
        $resourceAccess->setResource($resource);
        $resourceAccess->setRequester($requester);

        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        // testing that all roles are seen under ACCESS_SUPER_ADMIN
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        // testing with a logged in user

        /** @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');

        $securityContext->setToken(
            new UsernamePasswordToken(
                $requester, null, 'main', array('ROLE_USER')
            )
        );

        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource));

        // testing different roles, alone or in combination
        $resourceAccess->setAccessLevels([Roles::ACCESS_ADMIN_1]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([Roles::ACCESS_MODERATOR_1]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([Roles::ACCESS_EDIT_1]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([Roles::ACCESS_READ_1]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([Roles::ACCESS_ADMIN_2]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([Roles::ACCESS_REVIEWER_2]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([Roles::ACCESS_READ_REVIEW]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([Roles::ACCESS_READ_REVIEW, Roles::ACCESS_MODERATOR_1]);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($this->RAManager->isGranted(Roles::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($this->RAManager->isGranted(Roles::ACCESS_READ_REVIEW, $resource, $requester));

    }

    public function testGrantAccessWithInvalidGrantedBy()
    {
        $resource  = new Resource();
        $requester = new Requester();
        $user      = new Requester();

        $this->entityManager->persist($requester);
        $this->entityManager->persist($user);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $this->RAManager->grantAccess($requester, $resource, [Roles::ACCESS_SUPER_ADMIN], $user);
    }

    public function testGrantAccess()
    {
        $resource  = new Resource();
        $requester = new Requester();

        $this->entityManager->persist($resource);
        $this->entityManager->persist($requester);
        $this->entityManager->flush();

        $this->RAManager->grantAccess($requester, $resource, [Roles::ACCESS_READ_REVIEW]);
        $accessLevels = $this->resourceAccessRepository->getAccessLevels($requester, $resource);

        $this->assertTrue(in_array(Roles::ACCESS_READ_REVIEW, $accessLevels));

        $this->RAManager->grantAccess($requester, $resource, [Roles::ACCESS_EDIT_1, Roles::ACCESS_READ_2]);
        $accessLevels = $this->resourceAccessRepository->getAccessLevels($requester, $resource);

        $this->assertTrue(in_array(Roles::ACCESS_READ_REVIEW, $accessLevels));
        $this->assertTrue(in_array(Roles::ACCESS_READ_2, $accessLevels));
        $this->assertTrue(in_array(Roles::ACCESS_EDIT_1, $accessLevels));
    }

    public function testUpdateAccessLevelsWithInvalidRequester()
    {
        $resource  = new Resource();
        $requester = new Requester();

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $this->RAManager->updateAccessLevels($requester, $resource, [Roles::ACCESS_SUPER_ADMIN]);
    }

    public function testUpdateAccessLevels()
    {
        $resource       = new Resource();
        $requester      = new Requester();
        $resourceAccess = new ResourceAccess();

        $resourceAccess->setResource($resource)
            ->setRequester($requester)
            ->setAccessLevels([Roles::ACCESS_ADMIN_1, Roles::ACCESS_ADMIN_2]);

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->RAManager->updateAccessLevels($requester, $resource, [Roles::ACCESS_SUPER_ADMIN]);

        $accessLevels = $this->resourceAccessRepository->getAccessLevels($requester, $resource);

        $this->assertTrue(in_array(Roles::ACCESS_SUPER_ADMIN, $accessLevels));
        $this->assertFalse(in_array(Roles::ACCESS_ADMIN_1, $accessLevels));
        $this->assertFalse(in_array(Roles::ACCESS_ADMIN_2, $accessLevels));
    }

    public function testRemoveAccessLevelsWithInvalidRequester()
    {
        $resource  = new Resource();
        $requester = new Requester();

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $this->RAManager->removeAccessLevels($requester, $resource, [Roles::ACCESS_SUPER_ADMIN]);
    }

    public function testRemoveAccessLevels()
    {
        $resource       = new Resource();
        $requester      = new Requester();
        $resourceAccess = new ResourceAccess();

        $resourceAccess->setResource($resource)
            ->setRequester($requester)
            ->setAccessLevels([Roles::ACCESS_ADMIN_1, Roles::ACCESS_ADMIN_2]);

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $accessLevels = $this->resourceAccessRepository->getAccessLevels($requester, $resource);

        $this->assertTrue(in_array(Roles::ACCESS_ADMIN_1, $accessLevels));

        $this->RAManager->removeAccessLevels($requester, $resource, [Roles::ACCESS_ADMIN_1]);

        $accessLevels = $this->resourceAccessRepository->getAccessLevels($requester, $resource);

        $this->assertFalse(in_array(Roles::ACCESS_ADMIN_1, $accessLevels));
    }

    public function testRemoveAccessWithInvalidRequester()
    {
        $resource  = new Resource();
        $requester = new Requester();

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $this->RAManager->removeAccess($requester, $resource);
    }

    public function testRemoveAccess()
    {
        $resource       = new Resource();
        $requester      = new Requester();
        $resourceAccess = new ResourceAccess();

        $resourceAccess->setResource($resource)
            ->setRequester($requester)
            ->setAccessLevels([Roles::ACCESS_ADMIN_1, Roles::ACCESS_ADMIN_2]);

        $this->entityManager->persist($requester);
        $this->entityManager->persist($resource);
        $this->entityManager->persist($resourceAccess);
        $this->entityManager->flush();

        $this->RAManager->removeAccess($requester, $resource);

        $resourceAccess = $this->resourceAccessRepository->findOneBy(['requester' => $requester, 'resource' => $resource]);

        $this->assertNull($resourceAccess);
    }
}