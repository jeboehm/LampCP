<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Tests\Service;

use Jeboehm\Lampcp\CoreBundle\Service\ConfigBuilderCommandCollector;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ConfigBuilderCommandCollectorTest
 *
 * @package Jeboehm\Lampcp\CoreBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigBuilderCommandCollectorTest extends WebTestCase
{
    /**
     * Test dependency injection & compiler pass.
     */
    public function testGetBuilders()
    {
        $return = $this
            ->getService()
            ->getBuilders();

        $this->assertGreaterThan(0, count($return));
    }

    /**
     * Get service.
     *
     * @return ConfigBuilderCommandCollector
     */
    public function getService()
    {
        return $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_core.configbuildercommandcollector');
    }
}
