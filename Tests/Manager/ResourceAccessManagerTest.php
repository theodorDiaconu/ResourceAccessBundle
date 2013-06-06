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

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ));

        $resourceAccess = new ResourceAccess();
        $resourceAccess->setAccessLevel(ResourceAccess::ACCESS_ADMIN);
        $resourceAccess->setResource($resource);
        $resourceAccess->setRequester($requester);

        $em->persist($resourceAccess);
        $em->flush();

        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ));

        $resourceAccess->setAccessLevel(ResourceAccess::ACCESS_EDIT);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ));

        $resourceAccess->setAccessLevel(ResourceAccess::ACCESS_READ);
        $em->persist($resourceAccess);
        $em->flush();

        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_ADMIN));
        $this->assertFalse($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_EDIT));
        $this->assertTrue($RAManager->isGranted($requester, $resource, ResourceAccess::ACCESS_READ));
    }
}