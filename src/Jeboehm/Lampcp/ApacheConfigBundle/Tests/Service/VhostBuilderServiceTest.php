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

use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VhostBuilderServiceTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderServiceTest extends WebTestCase
{
    /**
     * Test getPhpFpmConfigBuilder().
     *
     * @param VhostBuilderService $service
     *
     * @dataProvider serviceProvider
     */
    public function testPhpFpmConfigBuilder(VhostBuilderService $service)
    {
        $builder = $service->getPhpFpmConfigBuilder();
        $this->assertInstanceOf('\Jeboehm\Lampcp\PhpFpmBundle\Service\ConfigBuilderService', $builder);
    }

    /**
     * Get service.
     *
     * @return VhostBuilderService
     */
    public function serviceProvider()
    {
        $service      = $this->getVhostBuilderService();
        $certificate  = new Certificate();
        $user         = new User();
        $defaultIpSsl = new IpAddress();
        $defaultIp    = new IpAddress();

        $certificate
            ->setName('normal')
            ->setCertificateFile('asd')
            ->setCertificateKeyFile('dodooodood');

        $user
            ->setName('jeff')
            ->setGroupname('jeff')
            ->setUid(1000)
            ->setGid(1000);

        $defaultIp
            ->setAlias('default, no ssl')
            ->setHasSsl(false)
            ->setIp('127.0.0.1')
            ->setPort(80);

        $defaultIpSsl
            ->setAlias('default, ssl')
            ->setHasSsl(true)
            ->setIp('127.0.0.1')
            ->setPort(443);

        $domain1 = new Domain();
        $domain2 = new Domain();
        $domain3 = new Domain();

        $domain1
            ->setDomain('lampcp1.de')
            ->setPath('/var/www/lampcp1.de')
            ->setParsePhp(true)
            ->setUser($user)
            ->setWebroot('htdocs')
            ->setCertificate($certificate);

        $domain2
            ->setDomain('lampcp2.de')
            ->setPath('/var/www/lampcp2.de')
            ->setParsePhp(true)
            ->setUser($user)
            ->setWebroot('htdocs');

        $domain3
            ->setDomain('lampcp3.de')
            ->setPath('/var/www/lampcp3.de')
            ->setParsePhp(false)
            ->setUser($user)
            ->setWebroot('htdocs')
            ->setCertificate($certificate);

        $domain1
            ->getIpaddress()
            ->add($defaultIp);

        $domain2
            ->getIpaddress()
            ->add($defaultIpSsl);

        $domain2
            ->getIpaddress()
            ->add($defaultIp);

        $domain3
            ->getIpaddress()
            ->add($defaultIpSsl);

        $subdomain1 = new Subdomain($domain1);
        $subdomain2 = new Subdomain($domain2);

        $subdomain1->setSubdomain('nopaste');
        $subdomain2->setSubdomain('wiki');

        $domain1
            ->getSubdomain()
            ->add($subdomain1);

        $domain2
            ->getSubdomain()
            ->add($subdomain2);

        $service->setDomains(
            array(
                 $domain1,
                 $domain2,
                 $domain3,
            )
        );

        return array(
            array($service)
        );
    }

    /**
     * Get vhost builder service.
     *
     * @return VhostBuilderService
     */
    protected function getVhostBuilderService()
    {
        /** @var VhostBuilderService $service */
        $service = $this
            ->createClient()
            ->getContainer()
            ->get('jeboehm_lampcp_apache_config_vhostbuilder');

        return $service;
    }

    /**
     * Test get / set domain.
     */
    public function testDomainGetterSetter()
    {
        $service = $this->getVhostBuilderService();
        $domain  = new Domain();
        $domains = array($domain);

        $domain->setDomain('test.de');
        $service->setDomains($domains);

        $this->assertEquals($domains, $service->getDomains());
    }

    /**
     * Test getConfigdir() empty exception.
     *
     * @expectedException \Jeboehm\Lampcp\ApacheConfigBundle\Exception\EmptyConfigPathException
     */
    public function testConfigdirEmptyGetterException()
    {
        $this
            ->getVhostBuilderService()
            ->getConfigdir();
    }

    /**
     * Test setConfigdir() invalid path exception.
     *
     * @expectedException \Jeboehm\Lampcp\ApacheConfigBundle\Exception\EmptyConfigPathException
     */
    public function testConfigdirEmptySetterException()
    {
        $this
            ->getVhostBuilderService()
            ->setConfigdir('nonexistent');
    }

    /**
     * Test get / set configdir.
     */
    public function testConfigdirGetterSetter()
    {
        $service = $this->getVhostBuilderService();
        $service->setConfigdir(sys_get_temp_dir());

        $this->assertEquals(sys_get_temp_dir(), $service->getConfigdir());
    }

    /**
     * Test setPhpSocketToVhosts().
     *
     * @param VhostBuilderService $service
     *
     * @dataProvider serviceProvider
     */
    public function testSetPhpSocketToVhosts(VhostBuilderService $service)
    {
        $service->collectVhostModels();
        $service->setPhpSocketToVhosts();

        foreach ($service->getVhosts() as $vhost) {
            if ($vhost->getPHPEnabled()) {
                $this->assertNotEmpty($vhost->getPhpFpmSocket());
            } else {
                $this->assertNull($vhost->getPhpFpmSocket());
            }
        }
    }

    /**
     * Test getIpAddresses().
     *
     * @param VhostBuilderService $service
     *
     * @dataProvider serviceProvider
     */
    public function testGetIpAddresses(VhostBuilderService $service)
    {
        $service->collectVhostModels();
        $ips = $service->getIpAddresses();

        $this->assertCount(2, $ips);
    }

    /**
     * Test getTwigEngine().
     */
    public function testGetTwigEngine()
    {
        $twig = $this
            ->getVhostBuilderService()
            ->getTwigEngine();
        $this->assertInstanceOf('Symfony\Bundle\TwigBundle\TwigEngine', $twig);
    }

    /**
     * Test renderConfiguration().
     *
     * @param VhostBuilderService $service
     *
     * @dataProvider serviceProvider
     */
    public function testRenderConfiguration(VhostBuilderService $service)
    {
        $service->collectVhostModels();
        $content = $service->renderConfiguration();

        $this->assertStringStartsWith('##', $content);
    }
}
