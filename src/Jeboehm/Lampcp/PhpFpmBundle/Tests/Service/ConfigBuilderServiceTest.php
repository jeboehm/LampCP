<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PhpFpmBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jeboehm\Lampcp\PhpFpmBundle\Service\ConfigBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\User;

/**
 * Class ConfigBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\PhpFpmBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigBuilderServiceTest extends WebTestCase {
    /** Expect this file in pool config generation. */
    const EXPECT_FILENAME = '/tmp/lampcp-pool-testneverexists.conf';

    /** @var ConfigBuilderService */
    protected $builder;

    /**
     * Set up.
     */
    public function setUp() {
        $this->builder = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_php_fpm.configbuilderservice');

        $cs = $this
            ->getMockBuilder('Jeboehm\Lampcp\ConfigBundle\Service\ConfigService')
            ->setMethods(array('getParameter'))
            ->getMock();

        $cs
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('/tmp'));

        $this->builder->setConfigservice($cs);
    }

    /**
     * Get test user.
     *
     * @return User
     */
    protected function _getUser() {
        $user = new User();
        $user
            ->setName('testneverexists')
            ->setGid(1000)
            ->setUid(1000)
            ->setGroupname('test');

        return $user;
    }

    /**
     * Checks if the pool configuration exists.
     */
    public function testCreatePool() {
        $this->builder->createPool($this->_getUser());

        $this->assertTrue(is_file(self::EXPECT_FILENAME));

        unlink(self::EXPECT_FILENAME);
    }

    /**
     * Checks if the pool configuration is deleted.
     */
    public function testDeleteOldPools() {
        $user = $this->_getUser();
        $user->setName('testneverexists');

        $this->builder->createPool($user);
        $this->assertTrue(is_file(self::EXPECT_FILENAME));

        $this->builder->deleteOldPools();
        $this->assertFalse(is_file(self::EXPECT_FILENAME));
    }
}
