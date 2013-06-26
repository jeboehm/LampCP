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
        $domain = new Domain();
        $db1    = new MysqlDatabase($domain);
        $db1
            ->setName('lampcpsql98')
            ->setComment('Test')
            ->setPassword('test123');

        $db2 = new MysqlDatabase($domain);
        $db2
            ->setName('lampcpsql99')
            ->setComment('Test')
            ->setPassword('test123');

        $this->service
            ->addEntity($db1)
            ->addEntity($db2);

        $this->assertNull($this->adapter->getDatabases()->findByName($db1->getName()));
        $this->assertNull($this->adapter->getDatabases()->findByName($db2->getName()));

        $this->service->createAndUpdateUsersAndDatabases();

        $this->assertNotNull($this->adapter->getDatabases()->findByName($db1->getName()));
        $this->assertNotNull($this->adapter->getDatabases()->findByName($db2->getName()));

        $this->service->createAndUpdateUsersAndDatabases();

        $this->assertNotNull($this->adapter->getDatabases()->findByName($db1->getName()));
        $this->assertNotNull($this->adapter->getDatabases()->findByName($db2->getName()));

        $this->service->findAndDeleteOldDatabases();

        $this->assertNotNull($this->adapter->getDatabases()->findByName($db1->getName()));
        $this->assertNotNull($this->adapter->getDatabases()->findByName($db2->getName()));
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
