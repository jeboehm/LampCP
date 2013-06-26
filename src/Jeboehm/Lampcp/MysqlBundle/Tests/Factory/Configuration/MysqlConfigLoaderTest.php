<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Tests\Factory\Configuration;

use Jeboehm\Lampcp\MysqlBundle\Factory\Configuration\MysqlConfigLoader;
use Jeboehm\Lampcp\MysqlBundle\Model\Configuration\MysqlConfiguration;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MysqlConfigLoaderTest
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Tests\Factory\Configuration
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class MysqlConfigLoaderTest extends WebTestCase
{
    /**
     * Test configuration factory.
     *
     * @group database
     */
    public function testManualFactory()
    {
        /** @var MysqlConfigLoader $service */
        $service = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.factory.configuration.mysqlconfigloader');

        $config = $service->factory();
        $result = $this->validateConfigurationModel($config);

        $this->assertTrue($result);
    }

    /**
     * @param MysqlConfiguration $config
     *
     * @return bool
     */
    private function validateConfigurationModel(MysqlConfiguration $config)
    {
        $this->assertNotEmpty($config->getUsername());
        $this->assertNotEmpty($config->getPort());
        $this->assertNotEmpty($config->getHost());

        return true;
    }

    /**
     * Test configuration factory via dependency injection.
     *
     * @group database
     */
    public function testDiFactory()
    {
        /** @var MysqlConfiguration $config */
        $config = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_mysql.model.configuration.mysqlconfiguration');

        $result = $this->validateConfigurationModel($config);

        $this->assertTrue($result);
    }
}
