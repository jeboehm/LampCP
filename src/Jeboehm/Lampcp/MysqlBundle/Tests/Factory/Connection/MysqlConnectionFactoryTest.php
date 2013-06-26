<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Tests\Factory\Connection;

use Jeboehm\Lampcp\MysqlBundle\Factory\Connection\MysqlConnectionFactory;
use Jeboehm\Lampcp\MysqlBundle\Model\Connection\MysqlConnection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MysqlConnectionFactoryTest
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Tests\Factory\Connection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlConnectionFactoryTest extends WebTestCase
{
    /**
     * Test manual connection factory.
     */
    public function testManualFactory()
    {
        /** @var MysqlConnectionFactory $factory */
        $factory = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.factory.connection.mysqlconnectionfactory');

        $this->assertInstanceOf('\Jeboehm\Lampcp\MysqlBundle\Model\Connection\MysqlConnection', $factory->factory());
    }

    /**
     * Test factory via dependency injection.
     */
    public function testDiFactory()
    {
        /** @var MysqlConnection */
        $model = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.root_connection');

        $this->assertInstanceOf('\Jeboehm\Lampcp\MysqlBundle\Model\Connection\MysqlConnection', $model);
    }
}
