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
}
