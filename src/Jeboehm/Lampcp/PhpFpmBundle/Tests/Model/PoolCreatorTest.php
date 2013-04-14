<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PhpFpmBundle\Tests\Model;

use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\PhpFpmBundle\Model\PoolCreator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * Class PoolCreatorTest
 *
 * @package Jeboehm\Lampcp\PhpFpmBundle\Tests
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class PoolCreatorTest extends WebTestCase {
    /** @var PoolCreator */
    public $creator;

    /**
     * Set up.
     */
    public function setUp() {
        /** @var $twig TwigEngine */
        $twig = $this
            ->createClient()
            ->getContainer()
            ->get('templating');
        $user = new User();

        $user
            ->setGid(1000)
            ->setUid(1000)
            ->setGroupname('test')
            ->setName('test');

        $this->creator = new PoolCreator($twig, sys_get_temp_dir(), $user);
    }

    /**
     * Get template file path.
     *
     * @return string
     */
    protected function _getTemplateFile() {
        return realpath(__DIR__ . '/../../Resources/views/pool/pool.conf.twig');
    }

    /**
     * Get pool name.
     */
    public function testGetPoolName() {
        $name = $this->creator->getPoolName();
        $this->assertEquals('LAMPCP-POOL-test', $name);
    }

    /**
     * Get pool configuration.
     */
    public function testGetPoolConfiguration() {
        $config         = $this->creator->getPoolConfiguration();
        $lengthTemplate = strlen(file_get_contents($this->_getTemplateFile())) - 100;

        $this->assertGreaterThan($lengthTemplate, strlen($config));
    }
}
