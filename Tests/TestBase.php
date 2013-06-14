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

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Symfony\Bridge\Doctrine\ContainerAwareEventManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Bundle\DoctrineBundle\Registry;

class TestBase extends WebTestCase
{
    protected static $client;
    protected $container;
    protected $entityManager;
    /** @var ResourceAccessManager $RAManager */
    protected $RAManager;

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
        $this->container = static::createClient()->getContainer();
        $this->RAManager = $this->container->get('resource_access_manager');

        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        /** @var ContainerAwareEventManager $evm */
        $evm = $doctrine->getManager()->getEventManager();

        $listeners = $evm->getListeners();

        foreach($listeners['loadClassMetadata'] as $listener) {
            if($listener instanceof ResolveTargetEntityListener) {
                $evm->removeEventListener('loadClassMetadata', $listener);
            }
        }

        $listeners = $evm->getListeners();

        $this->entityManager = $doctrine->getManager();
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getManager()->clear();
    }

    /**
     * @return Registry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }
}