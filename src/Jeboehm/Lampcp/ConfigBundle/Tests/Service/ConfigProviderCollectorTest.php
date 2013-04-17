<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigProviderCollector;

/**
 * Class ConfigProviderCollectorTest
 *
 * @package Jeboehm\Lampcp\ConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigProviderCollectorTest extends WebTestCase {
    /**
     * Test provider collection.
     */
    public function testProvider() {
        /** @var ConfigProviderCollector $collector */
        $collector = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_config_configprovidercollector');

        // Get providers.
        $services = $collector->getProviders();
        $this->assertGreaterThanOrEqual(1, count($services));

        // Add provider.
        $first       = array_pop($services);
        $countBefore = count($collector->getProviders());
        $collector->addProvider($first);
        $countAfter = count($collector->getProviders());
        $this->assertNotEquals($countBefore, $countAfter);
    }
}
