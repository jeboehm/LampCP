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

use Doctrine\Common\Collections\ArrayCollection;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class VhostBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderServiceTest extends WebTestCase
{
    /** @var VhostBuilderService */
    protected $_service;

    /**
     * Test getPhpFpmConfigBuilder().
     */
    public function testPhpFpmConfigBuilder()
    {
        $builder = $this
            ->_getService()
            ->getPhpFpmConfigBuilder();

        $this->assertInstanceOf('\Jeboehm\Lampcp\PhpFpmBundle\Service\ConfigBuilderService', $builder);
    }

    /**
     * Get vhost builder service.
     *
     * @return VhostBuilderService
     */
    protected function _getService()
    {
        if ($this->_service === null) {
            $this->_service = $this
                ->createClient()
                ->getContainer()
                ->get('jeboehm_lampcp_apache_config_vhostbuilder');
        }

        return $this->_service;
    }

    /**
     * Test getPhpSocket().
     */
    public function testGetPhpSocket()
    {
        $user = new User();
        $user->setName('tester');

        $domain = new Domain();
        $domain
            ->setPath('/var/www/test.de')
            ->setUser($user);

        $this->assertEquals(
            '/tmp/lampcp-php-fpm-tester.sock',
            $this
                ->_getService()
                ->getPhpSocket($domain)
        );
    }

    /**
     * Test setConfigDir() with valid directory.
     */
    public function testSetValidConfigDir()
    {
        $dir = sys_get_temp_dir();

        $this
            ->_getService()
            ->setConfigdir($dir);

        $this->assertEquals(
            $dir,
            $this
                ->_getService()
                ->getConfigdir()
        );
    }

    /**
     * Test setConfigDir() with invalid directory.
     *
     * @expectedException \Jeboehm\Lampcp\ApacheConfigBundle\Exception\EmptyConfigPathException
     */
    public function testSetInvalidConfigDir()
    {
        $this
            ->_getService()
            ->setConfigdir('/asdkjaskdj');
    }

    /**
     * Test getConfigDir() with empty directory.
     *
     * @expectedException \Jeboehm\Lampcp\ApacheConfigBundle\Exception\EmptyConfigPathException
     */
    public function testGetEmptyConfigDir()
    {
        $this
            ->_getService()
            ->getConfigdir();
    }

    /**
     * Test getTwigEngine().
     */
    public function testGetTwigEngine()
    {
        $this->assertInstanceOf(
            '\Symfony\Bridge\Twig\TwigEngine',
            $this
                ->_getService()
                ->getTwigEngine()
        );
    }

    /**
     * Test domain getter / setter.
     */
    public function testGetSetDomain()
    {
        $this
            ->_getService()
            ->setDomains($this->domainProvider());

        $this->assertCount(
            count($this->domainProvider()),
            $this
                ->_getService()
                ->getDomains()
        );
    }

    /**
     * Provides some test domains.
     *
     * @return array
     */
    public function domainProvider()
    {
        $user = new User();
        $user
            ->setName('tester')
            ->setGroupname('testgroup')
            ->setUid(1000)
            ->setGid(1000);

        $certificate = new Certificate();
        $ips         = $this->ipProvider();
        $domain1     = new Domain();
        $domain1
            ->setIpaddress(new ArrayCollection(array($ips[0], $ips[1])))
            ->setCertificate($certificate)
            ->setUser($user);

        $domain2 = new Domain();
        $domain2
            ->setIpaddress(new ArrayCollection(array($ips[1])))
            ->setUser($user);

        $domain3 = new Domain();
        $domain3
            ->setIpaddress(new ArrayCollection(array($ips[3], $ips[2])))
            ->setUser($user);

        return array($domain1, $domain2, $domain3);
    }

    /**
     * Provide some test ip addresses.
     *
     * @return array
     */
    public function ipProvider()
    {
        $ip1 = new IpAddress();
        $ip1
            ->setAlias('local, ssl')
            ->setIp('127.0.0.1')
            ->setPort(443)
            ->setHasSsl(true);

        $ip2 = new IpAddress();
        $ip2
            ->setAlias('local, normal')
            ->setIp('127.0.0.1')
            ->setPort(80);

        $ip3 = new IpAddress();
        $ip3
            ->setAlias('local v6, normal')
            ->setIp('::1')
            ->setPort(80);

        $ip4 = new IpAddress();
        $ip4
            ->setAlias('local v6, ssl')
            ->setIp('::1')
            ->setPort(443);

        return array($ip1, $ip2, $ip3, $ip4);
    }

    /**
     * Test subdomain getter / setter.
     */
    public function testGetSetSubdomain()
    {
        $this
            ->_getService()
            ->setSubdomains($this->subdomainProvider());

        $this->assertCount(
            count($this->subdomainProvider()),
            $this
                ->_getService()
                ->getSubdomains()
        );
    }

    /**
     * Provides test subdomains.
     *
     * @return array
     */
    public function subdomainProvider()
    {
        $domains    = $this->domainProvider();
        $cert       = new Certificate();
        $subdomain1 = new Subdomain($domains[0]);
        $subdomain2 = new Subdomain($domains[1]);

        $subdomain1
            ->setSubdomain('testa')
            ->setCertificate($cert);

        $subdomain2
            ->setSubdomain('testb')
            ->setCertificate($cert);

        return array($subdomain1, $subdomain2);
    }

    /**
     * Test collectVhostModels().
     */
    public function testCollectVhostModels()
    {
        $this
            ->_getService()
            ->setDomains($this->domainProvider())
            ->setSubdomains($this->subdomainProvider());

        $this
            ->_getService()
            ->collectVhostModels();

        $exCountVhosts = 0;

        foreach ($this->domainProvider() as $domain) {
            /** @var Domain $domain */
            $exCountVhosts += $domain
                ->getIpaddress()
                ->count();
        }

        foreach ($this->subdomainProvider() as $subdomain) {
            /** @var Subdomain $subdomain */
            $exCountVhosts += $subdomain
                ->getDomain()
                ->getIpaddress()
                ->count();
        }

        $this->assertCount(
            $exCountVhosts,
            $this
                ->_getService()
                ->getVhosts()
        );
    }

    /**
     * Test vhost sorting.
     * Non-Wildcard first, wildcard last.
     */
    public function testSortVhosts()
    {
        $domainWc = new Domain();
        $domainWc->setIsWildcard(true);

        $vhost1 = new Vhost();
        $vhost1->setDomain($domainWc);

        $vhost2 = new Vhost();
        $vhost2->setDomain(new Domain());

        $vhost3 = new Vhost();
        $vhost3->setDomain(new Domain());

        $this
            ->_getService()
            ->addVhost($vhost3)
            ->addVhost($vhost1)
            ->addVhost($vhost2);

        $vhosts_tmp = $this
            ->_getService()
            ->getVhosts();

        /** @var Vhost $last */
        $last = array_pop($vhosts_tmp);

        $this->assertFalse($last->getIsWildcard());

        $this
            ->_getService()
            ->sortVhosts();

        $vhosts_tmp = $this
            ->_getService()
            ->getVhosts();

        /** @var Vhost $last */
        $last = array_pop($vhosts_tmp);

        $this->assertTrue($last->getIsWildcard());
    }

    /**
     * Test getIpAdresses().
     */
    public function testGetIpAddresses()
    {
        $this
            ->_getService()
            ->setDomains($this->domainProvider());

        $this
            ->_getService()
            ->collectVhostModels();

        $this->assertCount(
            count($this->ipProvider()),
            $this
                ->_getService()
                ->getIpAddresses()
        );
    }

    /**
     * Test buildConfiguration().
     */
    public function testBuildConfiguration()
    {
        $this
            ->_getService()
            ->setDomains($this->domainProvider())
            ->setConfigdir(sys_get_temp_dir())
            ->collectVhostModels();

        $this
            ->_getService()
            ->buildConfiguration();

        $filename = sys_get_temp_dir() . '/' . VhostBuilderService::vhostConfigFilename;

        $this->assertFileExists($filename);
        $this->assertGreaterThan(400, filesize($filename));
    }

    public function testSetPhpSocketToVhosts()
    {
        $user = new User();
        $user
            ->setName('tester')
            ->setGroupname('testgroup')
            ->setUid(1000)
            ->setGid(1000);

        $phpdomain = new Domain();
        $domain    = new Domain();

        $phpdomain
            ->setParsePhp(true)
            ->setUser($user);
        $domain
            ->setParsePhp(false)
            ->setUser($user);

        $vhost1 = new Vhost();
        $vhost2 = new Vhost();
        $vhost3 = new Vhost();

        $vhost1->setDomain($phpdomain);
        $vhost2->setDomain($domain);
        $vhost3->setDomain($phpdomain);

        $this
            ->_getService()
            ->setVhosts(array($vhost1, $vhost2, $vhost3))
            ->setPhpSocketToVhosts();

        $phpHosts   = 0;
        $noPhpHosts = 0;

        foreach ($this
                     ->_getService()
                     ->getVhosts() as $vhost) {
            if ($vhost->getPHPEnabled()) {
                $this->assertNotEmpty($vhost->getPhpFpmSocket());
                $phpHosts++;
            } else {
                $this->assertEmpty($vhost->getPhpFpmSocket());
                $noPhpHosts++;
            }
        }

        $this->assertEquals(2, $phpHosts);
        $this->assertEquals(1, $noPhpHosts);
    }

    /**
     * Tear down.
     */
    protected function tearDown()
    {
        $fs       = new Filesystem();
        $filename = sys_get_temp_dir() . '/' . VhostBuilderService::vhostConfigFilename;

        if ($fs->exists($filename)) {
            $fs->remove($filename);
        }

        $this->_service = null;

        parent::tearDown();
    }
}
