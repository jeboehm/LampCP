<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Tests\Adapter;

use Jeboehm\Lampcp\MysqlBundle\Adapter\MysqlAdapter;
use Jeboehm\Lampcp\MysqlBundle\Model\Database;
use Jeboehm\Lampcp\MysqlBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MysqlAdapterTest
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Tests\Adapter
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlAdapterTest extends WebTestCase
{
    /** @var MysqlAdapter */
    private $adapter;

    /**
     * Set up.
     */
    public function setUp()
    {
        parent::setUp();

        $this->adapter = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.adapter.mysqladapter');
    }

    /**
     * Test getDatabases.
     *
     * @group database
     */
    public function testGetDatabases()
    {
        $databases = $this->adapter->getDatabases();

        $this->assertGreaterThan(0, count($databases));
    }

    /**
     * Test getUsers.
     *
     * @group database
     */
    public function testGetUsers()
    {
        $users = $this->adapter->getUsers();

        $this->assertGreaterThan(0, count($users));
    }

    /**
     * Test createDatabase.
     *
     * @group database
     */
    public function testCreateDatabase()
    {
        $db = new Database();
        $db->setName('lampcp_' . rand(1, 9999));

        $result = $this->adapter->createDatabase($db);
        $this->assertTrue($result);

        return $db;
    }

    /**
     * Test deleteDatabase.
     *
     * @depends testCreateDatabase
     * @group database
     */
    public function testDropDatabase(Database $db)
    {
        $result = $this->adapter->deleteDatabase($db);

        $this->assertTrue($result);
    }

    /**
     * Delete unknown database.
     *
     * @group database
     */
    public function testDropDatabaseUnknown()
    {
        $model = new Database();
        $model->setName('dfghjkrtzui' . rand(1, 9999));

        $result = $this->adapter->deleteDatabase($model);
        $this->assertFalse($result);
    }

    /**
     * Test createUser.
     *
     * @group database
     */
    public function testCreateUser()
    {
        $user = new User();
        $user
            ->setName('lampcp_' . rand(1, 9999))
            ->setHost('127.0.0.1')
            ->setPassword('test123');

        $result = $this->adapter->createUser($user);

        $this->assertTrue($result);

        return $user;
    }

    /**
     * Test updateUser.
     *
     * @depends testCreateUser
     * @group database
     */
    public function testUpdateUser(User $user)
    {
        $user->setPassword('test321');

        $result = $this->adapter->updateUser($user);

        $this->assertTrue($result);

        return $user;
    }

    /**
     * Test deleteUser.
     *
     * @depends testUpdateUser
     * @group database
     */
    public function testDeleteUser(User $user)
    {
        $result = $this->adapter->deleteUser($user);

        $this->assertTrue($result);
    }

    /**
     * Delete unknown user.
     *
     * @group database
     */
    public function testDeleteUserUnknown()
    {
        $model = new User();
        $model
            ->setName('asdfghjrtzui' . rand(1, 9999))
            ->setHost('8.8.8.8');

        $result = $this->adapter->deleteUser($model);

        $this->assertFalse($result);
    }

    /**
     * Test updateDatabase.
     *
     * @group database
     */
    public function testUpdateDatabase()
    {
        $database = new Database();
        $user1    = new User();
        $user2    = new User();

        $user1
            ->setName('lampcp_user1')
            ->setHost('127.0.0.1')
            ->setPassword('test123');

        $user2
            ->setName('lampcp_user2')
            ->setHost('8.8.8.8')
            ->setPassword('test321');

        $database
            ->setName('lampcp_' . rand(1, 9999))
            ->addUser($user1)
            ->addUser($user2);

        $this->adapter->createUser($user1);
        $this->adapter->createUser($user2);

        // Create
        $result1 = $this->adapter->createDatabase($database);
        $this->assertTrue($result1);

        $database
            ->getUsers()
            ->removeElement($user2);

        // Update
        $result2 = $this->adapter->updateDatabase($database);
        $this->assertTrue($result2);

        // Delete
        $this->adapter->deleteDatabase($database);
        $this->adapter->deleteUser($user1);
        $this->adapter->deleteUser($user2);
    }
}
