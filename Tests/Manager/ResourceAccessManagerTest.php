<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
 */

namespace AT\ResourceAccessBundle\Tests\Manager;

use AT\ResourceAccessBundle\Tests\TestBase;
use AT\ResourceAccessBundle\Entity\Requester;
use AT\ResourceAccessBundle\Entity\Resource;
use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Manager\ResourceAccessManager;

class ResourceAccessManagerTest extends TestBase
{
    public function testIsGranted()
    {
        $resource = new Resource();
        $requester = new Requester();
        $em = static::getDoctrine()->getManager();
        /** @var ResourceAccessManager $RAManager */
        $RAManager = $this->container->get('resource_access_manager');

        $em->persist($requester);
        $em->persist($resource);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess = new ResourceAccess();
        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_SUPER_ADMIN]);
        $resourceAccess->setResource($resource);
        $resourceAccess->setRequester($requester);

        $em->persist($resourceAccess);
        $em->flush();

        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_ADMIN_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_MODERATOR_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_EDIT_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_READ_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_ADMIN_2]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_REVIEWER_2]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_READ_REVIEW]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

        $resourceAccess->setAccessLevels([ResourceAccess::ACCESS_READ_REVIEW, ResourceAccess::ACCESS_MODERATOR_1]);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_SUPER_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_MODERATOR_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_REVIEWER_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_2));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT_REVIEW));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_1));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_2));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ_REVIEW));

    }
}