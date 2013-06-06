<?php

namespace AT\ResourceAccessBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Driver\Connection;

class TestBase extends WebTestCase
{
    protected static $client;
    protected $container;

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