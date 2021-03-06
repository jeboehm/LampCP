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

use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\PhpFpmBundle\Service\ConfigBuilderService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ConfigBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\PhpFpmBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigBuilderServiceTest extends WebTestCase
{
    /** @var string */
    protected $_expectFilename;
    /** @var ConfigBuilderService */
    protected $builder;

    /**
     * Set up.
     */
    public function setUp()
    {
        $this->_expectFilename = sys_get_temp_dir() . '/lampcp-pool-testneverexists.conf';

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
            ->will($this->returnValue(sys_get_temp_dir()));

        $this->builder->setConfigservice($cs);
    }

    /**
     * Checks if the pool configuration exists.
     */
    public function testCreatePool()
    {
        $this->builder->createPool($this->_getUser());

        $this->assertFileExists($this->_expectFilename);

        unlink($this->_expectFilename);
    }

    /**
     * Get test user.
     *
     * @return User
     */
    protected function _getUser()
    {
        $user = new User();
        $user
            ->setName('testneverexists')
            ->setGid(1000)
            ->setUid(1000)
            ->setGroupname('test');

        return $user;
    }

    /**
     * Checks if the pool configuration is deleted.
     *
     * @group database
     */
    public function testDeleteOldPools()
    {
        $user = $this->_getUser();
        $user->setName('testneverexists');

        $this->builder->createPool($user);
        $this->assertFileExists($this->_expectFilename);

        $this->builder->deleteOldPools();
        $this->assertFileNotExists($this->_expectFilename);
    }
}
