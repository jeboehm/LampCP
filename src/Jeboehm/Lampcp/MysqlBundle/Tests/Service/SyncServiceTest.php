<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jeboehm\Lampcp\MysqlBundle\Adapter\AdapterInterface;
use Jeboehm\Lampcp\MysqlBundle\Model\Database;
use Jeboehm\Lampcp\MysqlBundle\Model\User;
use Jeboehm\Lampcp\MysqlBundle\Service\SyncService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SyncServiceTest
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class SyncServiceTest extends WebTestCase
{
    /** @var SyncService */
    private $service;
    /** @var EntityManager */
    private $em;
    /** @var AdapterInterface */
    private $adapter;

    /**
     * Set up.
     */
    public function setUp()
    {
        $client        = $this->createClient();
        $this->service = $client
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.service.syncservice');
        $this->em      = $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager');
        $this->adapter = $client
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.adapter.mysqladapter');
    }

    /**
     * Test create database.
     *
     * @group database
     */
    public function testCreateDatabases()
    {
        // TODO
    }

    /**
     * Test delete old users.
     *
     * @group database
     */
    public function testDeleteOldUsers()
    {
        $user = new User();
        $user
            ->setName('lampcpsql99')
            ->setPassword('test123')
            ->setHost('localhost');

        $this->adapter->createUser($user);

        $this->assertNotNull(
            $this->adapter
                ->getUsers()
                ->findByName('lampcpsql99')
        );

        $this->service->findAndDeleteOldUsers();

        $this->assertNull(
            $this->adapter
                ->getUsers()
                ->findByName('lampcpsql99')
        );
    }

    /**
     * Test delete old databases
     *
     * @group database
     */
    public function testDeleteOldDatabases()
    {
        $database = new Database();
        $database->setName('lampcpsql99');

        $this->adapter->createDatabase($database);

        $this->assertNotNull(
            $this->adapter
                ->getDatabases()
                ->findByName('lampcpsql99')
        );

        $this->service->findAndDeleteOldDatabases();

        $this->assertNull(
            $this->adapter
                ->getDatabases()
                ->findByName('lampcpsql99')
        );
    }
}
