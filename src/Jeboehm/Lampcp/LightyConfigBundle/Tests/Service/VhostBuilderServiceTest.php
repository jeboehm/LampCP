<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\LightyConfigBundle\Service\VhostBuilderService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VhostBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderServiceTest extends WebTestCase
{
    /** @var VhostBuilderService */
    private $_service;

    /**
     * Set up.
     */
    public function setUp()
    {
        /** @var VhostBuilderService $service */
        $this->_service = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_lighty_config_vhostbuilder');

        $ip = new IpAddress();
        $ip
            ->setAlias('test')
            ->setIp('127.0.0.1')
            ->setPort(80);

        $user = new User();
        $user
            ->setName('test')
            ->setGroupname('test')
            ->setUid(1000)
            ->setGid(1000);

        $domain = new Domain();
        $domain
            ->setDomain('test.de')
            ->setPath(sys_get_temp_dir() . '/test.de')
            ->setUser($user)
            ->setIpaddress(new ArrayCollection(array($ip)));

        $subdomain = new Subdomain($domain);
        $subdomain->setSubdomain('test');

        $this
            ->getService()
            ->setDomains(array($domain))
            ->setSubdomains(array($subdomain))
            ->setConfigdir(sys_get_temp_dir());
    }

    /**
     * Get service.
     *
     * @return VhostBuilderService
     */
    protected function getService()
    {
        return $this->_service;
    }

    /**
     * Test collectVhostModels().
     */
    public function testCollectVhostModels()
    {
        $this
            ->getService()
            ->collectVhostModels();

        $vhosts = $this
            ->getService()
            ->getVhosts();

        $this->assertCount(2, $vhosts);
    }

    /**
     * Test renderConfiguration().
     */
    public function testRenderConfiguration()
    {
        $this
            ->getService()
            ->collectVhostModels();

        $config = $this
            ->getService()
            ->renderConfiguration();

        $this->assertStringEndsWith('}', trim($config));
    }
}
