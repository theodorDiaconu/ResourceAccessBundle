<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
 */

namespace AT\ResourceAccessBundle\Tests\Manager;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AT\ResourceAccessBundle\Tests\TestBase;
use AT\ResourceAccessBundle\Entity\Requester;
use AT\ResourceAccessBundle\Entity\Resource;
use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Manager\ResourceAccessManager;
use AT\ResourceAccessBundle\Repository\ResourceAccessRepository;

class ResourceAccessManagerTest extends TestBase
{
    public function testIsGrantedWithInvalidUser()
    {
        $resource = new Resource();
        $requester = new Requester();
        $em = static::getDoctrine()->getManager();
        /** @var ResourceAccessManager $RAManager */
        $RAManager = $this->container->get('resource_access_manager');

        $em->persist($requester);
        $em->persist($resource);
        $em->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource);
    }

    public function testIsGranted()
    {
        $em = static::getDoctrine()->getManager();
        /** @var ResourceAccessManager $RAManager */
        $RAManager = $this->container->get('resource_access_manager');

        $resource = new Resource();
        $requester = new Requester();

        $em->persist($requester);
        $em->persist($resource);
        $em->flush();

        // testing there is no access without a resourceAccess
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess = new ResourceAccess();
        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_SUPER_ADMIN]);
        $resourceAccess->setResource($resource);
        $resourceAccess->setRequester($requester);

        $em->persist($resourceAccess);
        $em->flush();

        // testing that all roles are seen under ACCESS_SUPER_ADMIN
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        // testing with a logged in user

        /** @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');

        $securityContext->setToken(
            new UsernamePasswordToken(
                $requester, null, 'main', array('ROLE_USER')
            )
        );

        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource));

        // testing different roles, alone or in combination
        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_ADMIN_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_MODERATOR_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_EDIT_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_READ_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_ADMIN_2]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_REVIEWER_2]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_READ_REVIEW]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_READ_REVIEW, ResourceAccess::ACCESS_MODERATOR_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_SUPER_ADMIN, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_ADMIN_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_MODERATOR_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_REVIEWER_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_2, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_EDIT_REVIEW, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_1, $resource, $requester));
        $this->assertFalse($RAManager->isGranted(ResourceAccess::ACCESS_READ_2, $resource, $requester));
        $this->assertTrue($RAManager->isGranted(ResourceAccess::ACCESS_READ_REVIEW, $resource, $requester));

    }

    public function testGrantAccessWithInvalidGrantedBy()
    {
        $em = static::getDoctrine()->getManager();
        /** @var ResourceAccessManager $RAManager */
        $RAManager = $this->container->get('resource_access_manager');

        $resource = new Resource();
        $requester = new Requester();
        $user = new Requester();

        $em->persist($requester);
        $em->persist($user);
        $em->persist($resource);
        $em->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $RAManager->grantAccess($requester, $resource, [ResourceAccess::ACCESS_SUPER_ADMIN], $user);
    }

    public function testGrantAccess()
    {
        $em = static::getDoctrine()->getManager();
        /** @var ResourceAccessRepository $rm */
        $rm = static::getDoctrine()->getRepository('ATResourceAccessBundle:ResourceAccess');
        /** @var ResourceAccessManager $RAManager */
        $RAManager = $this->container->get('resource_access_manager');

        $resource = new Resource();
        $requester = new Requester();

        $em->persist($resource);
        $em->persist($requester);
        $em->flush();


        $RAManager->grantAccess($requester, $resource, [ResourceAccess::ACCESS_READ_REVIEW]);
        $accessLevels = $rm->getAccessLevels($requester, $resource);

        $this->assertTrue(in_array(ResourceAccess::ACCESS_READ_REVIEW, $accessLevels));

        $RAManager->grantAccess($requester, $resource, [ResourceAccess::ACCESS_EDIT_1, ResourceAccess::ACCESS_READ_2]);
        $accessLevels = $rm->getAccessLevels($requester, $resource);

        $this->assertTrue(in_array(ResourceAccess::ACCESS_READ_REVIEW, $accessLevels));
        $this->assertTrue(in_array(ResourceAccess::ACCESS_READ_2, $accessLevels));
        $this->assertTrue(in_array(ResourceAccess::ACCESS_EDIT_1, $accessLevels));
    }

    public function testUpdateAccessLevelsWithInvalidRequester()
    {
        $em = static::getDoctrine()->getManager();
        /** @var ResourceAccessManager $RAManager */
        $RAManager = $this->container->get('resource_access_manager');

        $resource = new Resource();
        $requester = new Requester();

        $em->persist($requester);
        $em->persist($resource);
        $em->flush();

        $this->setExpectedException('Symfony\Component\Validator\Exception\InvalidArgumentException');

        $RAManager->updateAccessLevels($requester, $resource, [ResourceAccess::ACCESS_SUPER_ADMIN]);
    }

    public function testUpdateAccessLevels()
    {
        $em = static::getDoctrine()->getManager();
        /** @var ResourceAccessRepository $rm */
        $rm = static::getDoctrine()->getRepository('ATResourceAccessBundle:ResourceAccess');
        /** @var ResourceAccessManager $RAManager */
        $RAManager = $this->container->get('resource_access_manager');

        $resource = new Resource();
        $requester = new Requester();
        $resourceAccess = new ResourceAccess();

        $resourceAccess->setResource($resource)
            ->setRequester($requester)
            ->setAccessLevels([ResourceAccess::ACCESS_ADMIN_1, ResourceAccess::ACCESS_ADMIN_2])
        ;

        $em->persist($requester);
        $em->persist($resource);
        $em->persist($resourceAccess);
        $em->flush();

        $RAManager->updateAccessLevels($requester, $resource, [ResourceAccess::ACCESS_SUPER_ADMIN]);

        $accessLevels = $rm->getAccessLevels($requester, $resource);

        $this->assertTrue(in_array(ResourceAccess::ACCESS_SUPER_ADMIN, $accessLevels));
        $this->assertFalse(in_array(ResourceAccess::ACCESS_ADMIN_1, $accessLevels));
        $this->assertFalse(in_array(ResourceAccess::ACCESS_ADMIN_2, $accessLevels));
    }
}