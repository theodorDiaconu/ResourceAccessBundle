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
        $this->RAManager                = $this->container->get('resource_access_manager');
        $this->entityManager            = $this->container->get('doctrine')->getManager();
        $this->resourceAccessRepository = $this->entityManager->getRepository("ATResourceAccessBundle:ResourceAccess");
    }

    public function tearDown()
    {
        $this->entityManager->clear();
    }
}