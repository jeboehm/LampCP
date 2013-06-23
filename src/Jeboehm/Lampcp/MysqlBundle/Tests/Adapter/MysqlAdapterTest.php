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
     */
    public function testGetDatabases()
    {
        $databases = $this->adapter->getDatabases();

        $this->assertGreaterThan(0, count($databases));
    }

    /**
     * Test getUsers.
     */
    public function testGetUsers()
    {
        $users = $this->adapter->getUsers();

        $this->assertGreaterThan(0, count($users));
    }
}
