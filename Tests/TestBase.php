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

namespace AT\ResourceAccessBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use AT\ResourceAccessBundle\Manager\ResourceAccessManager;
use AT\ResourceAccessBundle\Tests\Model\Roles;
use AT\ResourceAccessBundle\Repository\ResourceAccessRepository;

class TestBase extends WebTestCase
{
    protected static $client;
    /** @var ContainerInterface */
    protected $container;
    /** @var EntityManager */
    protected $entityManager;
    /** @var ResourceAccessManager $RAManager */
    protected $RAManager;
    /** @var ResourceAccessRepository */
    protected $resourceAccessRepository;

    public static function setUpBeforeClass()
    {
        self::$client = static::createClient();
        self::clearDatabase();
    }

    protected static function clearDatabase()
    {
        /** @var Registry $doctrine */
        $doctrine = self::createClient()->getContainer()->get('doctrine');

        /** @var Connection $connection */
        $connection = $doctrine->getConnection();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');

        $tables = $connection->getSchemaManager()->listTableNames();
        foreach ($tables as $table) {
            $connection->executeQuery(sprintf('TRUNCATE TABLE %s', $table));
        }

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public static function tearDownAfterClass()
    {
        self::clearDatabase();
    }

    public function setUp()
    {
        $this->container                = static::createClient()->getContainer();
        $this->entityManager            = $this->container->get('doctrine')->getManager();
        $this->resourceAccessRepository = $this->entityManager->getRepository("ATResourceAccessBundle:ResourceAccess");
        $roleHierarchies                = $this->getRoles();
        $this->RAManager                = new ResourceAccessManager($this->entityManager, $this->container->get('security.context'), $roleHierarchies, $this->container->getParameter('kernel.cache_dir'));
        $this->RAManager->load($this->container->getParameter('kernel.cache_dir'), $roleHierarchies, true);
    }

    public function tearDown()
    {
        $this->entityManager->clear();
    }

    public function getRoles()
    {
        return ['AT\ResourceAccessBundle\Entity\Resource' => [
            'role_hierarchy' => [
                Roles::ACCESS_SUPER_ADMIN => [
                    Roles::ACCESS_ADMIN_1,
                    Roles::ACCESS_ADMIN_2
                ],
                Roles::ACCESS_ADMIN_1     => [
                    Roles::ACCESS_MODERATOR_1
                ],
                Roles::ACCESS_MODERATOR_1 => [
                    Roles::ACCESS_EDIT_1
                ],
                Roles::ACCESS_EDIT_1      => [
                    Roles::ACCESS_READ_1
                ],
                Roles::ACCESS_ADMIN_2     => [
                    Roles::ACCESS_MODERATOR_2,
                    Roles::ACCESS_REVIEWER_2
                ],
                Roles::ACCESS_MODERATOR_2 => [
                    Roles::ACCESS_EDIT_2
                ],
                Roles::ACCESS_EDIT_2      => [
                    Roles::ACCESS_READ_2
                ],
                Roles::ACCESS_REVIEWER_2  => [
                    Roles::ACCESS_READ_REVIEW,
                    Roles::ACCESS_EDIT_REVIEW
                ]
            ]
        ]];
    }
}