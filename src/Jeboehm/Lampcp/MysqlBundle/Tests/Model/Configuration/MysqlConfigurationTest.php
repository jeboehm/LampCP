<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Tests\Model\Configuration;

use Jeboehm\Lampcp\MysqlBundle\Model\Configuration\MysqlConfiguration;

/**
 * Class MysqlConfigurationTest
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Tests\Model\Configuration
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setPort.
     */
    public function testSetPort()
    {
        $config = new MysqlConfiguration();
        $config->setPort('3306');

        $this->assertEquals(3306, $config->getPort());
    }
}
