<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service;

use Jeboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DirectoryBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DirectoryBuilderServiceTest extends WebTestCase
{
    const _dirs = 'conf,htdocs,logs,tmp';

    /**
     * Test createDirectories().
     */
    public function testCreateDirectorysForDomain()
    {
        $domain  = $this->_getDomain();
        $fs      = new Filesystem();
        $service = $this->_getService();

        $service->createDirectories($domain);

        foreach (explode(',', self::_dirs) as $dir) {
            $path = $this->_getTestPath() . '/' . $dir;
            $this->assertTrue($fs->exists($path));
        }
    }

    /**
     * Tear down.
     */
    protected function tearDown()
    {
        parent::tearDown();

        $fs = new Filesystem();
        $fs->remove($this->_getTestPath());
    }

    /**
     * Get domain.
     *
     * @return Domain
     */
    protected function _getDomain()
    {
        $user   = new User();
        $domain = new Domain();

        $user
            ->setName('www-data')
            ->setUid(1000)
            ->setGid(1000)
            ->setGroupname('www-data');

        $domain
            ->setUser($user)
            ->setPath($this->_getTestPath());

        return $domain;
    }

    /**
     * Get test path.
     *
     * @return string
     */
    protected function _getTestPath()
    {
        $fs       = new Filesystem();
        $testpath = sys_get_temp_dir() . '/testdomain';

        if (!$fs->exists($testpath)) {
            $fs->mkdir($testpath);
        }

        return $testpath;
    }

    /**
     * Get service.
     *
     * @return DirectoryBuilderService
     */
    protected function _getService()
    {
        return $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_directorybuilder');
    }
}
