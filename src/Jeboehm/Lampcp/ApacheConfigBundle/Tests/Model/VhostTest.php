<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Tests\Model;

use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Entity\User;

/**
 * Class VhostTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getServerName().
     */
    public function testGetServerName()
    {
        $domain = new Domain();
        $domain->setDomain('lampcp.de');

        $subdomain = new Subdomain($domain);
        $subdomain->setSubdomain('test');

        $vhost = new Vhost();
        $vhost->setDomain($domain);

        $this->assertEquals('lampcp.de', $vhost->getServerName());

        $vhost->setSubdomain($subdomain);
        $this->assertEquals('test.lampcp.de', $vhost->getServerName());
    }

    /**
     * Test getDocumentRoot().
     */
    public function testGetDocumentRoot()
    {
        $domain = new Domain();
        $domain
            ->setPath('/tmp/domain.de')
            ->setWebroot('htdocs');

        $subdomain = new Subdomain($domain);
        $subdomain->setPath('htdocs/subdomain');

        $vhost = new Vhost();
        $vhost->setDomain($domain);

        $this->assertEquals('/tmp/domain.de/htdocs', $vhost->getDocumentRoot());

        $vhost->setSubdomain($subdomain);
        $this->assertEquals('/tmp/domain.de/htdocs/subdomain', $vhost->getDocumentRoot());
    }

    /**
     * Test getPHPEnabled().
     */
    public function testGetPHPEnabled()
    {
        $domain    = new Domain();
        $subdomain = new Subdomain($domain);
        $vhost     = new Vhost();
        $vhost->setDomain($domain);

        $domain->setParsePhp(true);
        $this->assertTrue($vhost->getPHPEnabled());

        $domain->setRedirectUrl('asd');
        $this->assertFalse($vhost->getPHPEnabled());

        $vhost->setSubdomain($subdomain);
        $this->assertTrue($vhost->getPHPEnabled());
    }

    /**
     * Test getCertificate().
     */
    public function testGetCertificate()
    {
        $domain    = new Domain();
        $subdomain = new Subdomain($domain);
        $vhost     = new Vhost();
        $certA     = new Certificate();
        $certB     = new Certificate();

        $ip = new IpAddress();
        $ip->setHasSsl(true);

        $vhost
            ->setDomain($domain)
            ->setIpaddress($ip);

        $subdomain->setCertificate($certA);

        $certA->setName('A');
        $certB->setName('B');

        $this->assertNull($vhost->getCertificate());

        $domain->setCertificate($certB);
        $this->assertEquals(
            'B',
            $vhost
                ->getCertificate()
                ->getName()
        );

        $vhost->setSubdomain($subdomain);
        $this->assertEquals(
            'A',
            $vhost
                ->getCertificate()
                ->getName()
        );
    }

    /**
     * Test getAccessLog(), getErrorLog().
     */
    public function testGetLog()
    {
        $domain = new Domain();
        $vhost  = new Vhost();

        $domain->setPath('/var/www/test.de');
        $vhost->setDomain($domain);

        $this->assertEquals('/var/www/test.de/logs/access.log', $vhost->getAccessLog());
        $this->assertEquals('/var/www/test.de/logs/error.log', $vhost->getErrorLog());
    }

    /**
     * Test getCustomConfig().
     */
    public function testGetCustomConfig()
    {
        $vhost     = new Vhost();
        $domain    = new Domain();
        $subdomain = new Subdomain($domain);

        $domain->setCustomconfig('a');
        $subdomain->setCustomconfig('b');

        $vhost->setDomain($domain);

        $this->assertEquals('a', $vhost->getCustomConfig());

        $vhost->setSubdomain($subdomain);

        $this->assertEquals('b', $vhost->getCustomConfig());
    }

    /**
     * Test getIpAddress().
     */
    public function testGetIpAddress()
    {
        $vhost = new Vhost();
        $ip    = $vhost->getIpaddress();

        $this->assertEquals('*', $ip->getIp());
    }

    /**
     * Test getServerAlias()
     */
    public function testGetServerAlias()
    {
        $vhost     = new Vhost();
        $domain    = new Domain();
        $subdomain = new Subdomain($domain);

        $domain->setDomain('lampcp.de');
        $subdomain->setSubdomain('test');

        // www.lampcp.de
        $vhost->setDomain($domain);
        $result0 = $vhost->getServerAlias();

        // *.lampcp.de
        $domain->setIsWildcard(true);
        $result1 = $vhost->getServerAlias();

        // www.test.lampcp.de
        $vhost->setSubdomain($subdomain);
        $result2 = $vhost->getServerAlias();

        // *.test.lampcp.de
        $subdomain->setIsWildcard(true);
        $result3 = $vhost->getServerAlias();

        $this->assertEquals('www.lampcp.de', array_pop($result0));
        $this->assertEquals('*.lampcp.de', array_pop($result1));
        $this->assertEquals('www.test.lampcp.de', array_pop($result2));
        $this->assertEquals('*.test.lampcp.de', array_pop($result3));
    }

    /**
     * Test getSuexecUserGroup().
     */
    public function testGetSuexecUserGroup()
    {
        $user = new User();
        $user
            ->setName('jeff')
            ->setGroupname('staff');

        $domain = new Domain();
        $domain->setUser($user);

        $vhost = new Vhost();
        $vhost->setDomain($domain);

        $this->assertEquals('jeff staff', $vhost->getSuexecUserGroup());
    }

    /**
     * Test getDomain().
     */
    public function testGetDomain()
    {
        $vhost = new Vhost();
        $vhost->setDomain(new Domain());
        $this->assertNotNull($vhost->getDomain());
    }

    /**
     * Test getPhpFpmSocket(), setPhpFpmSocket().
     */
    public function testPhpFpmSocket()
    {
        $vhost = new Vhost();
        $vhost->setPhpFpmSocket('test');
        $this->assertEquals('test', $vhost->getPhpFpmSocket());
    }

    /**
     * Test getForceSSL().
     */
    public function testGetForceSSL()
    {
        $domain      = new Domain();
        $vhost       = new Vhost();
        $certificate = new Certificate();
        $subdomain   = new Subdomain($domain);
        $ip          = new IpAddress();

        $certificate
            ->setName('test')
            ->setCertificateFile('asdasdasdds');

        $domain
            ->setCertificate($certificate)
            ->setForceSsl(true);

        $subdomain
            ->setCertificate($certificate)
            ->setForceSsl(true);

        $vhost->setDomain($domain);

        $this->assertTrue($vhost->getForceSSL());

        $vhost->setSubdomain($subdomain);
        $this->assertTrue($vhost->getForceSSL());

        $ip->setHasSsl(true);
        $vhost->setIpaddress($ip);
        $this->assertFalse($vhost->getForceSSL());
    }

    /**
     * Test vhost address generation.
     *
     * @param Vhost  $vhost
     * @param string $expect
     *
     * @dataProvider providerTestGetVhostAddress
     */
    public function testGetVhostAddress(Vhost $vhost, $expect)
    {
        $this->assertEquals($expect, $vhost->getVhostAddress());
    }

    /**
     * Dataprovider for testGetVhostAddress.
     *
     * @return array
     */
    public function providerTestGetVhostAddress()
    {
        $ip4    = new IpAddress();
        $vhost4 = new Vhost();
        $ip6    = new IpAddress();
        $vhost6 = new Vhost();

        $ip4
            ->setIp('127.0.0.1')
            ->setPort(80);
        $vhost4->setIpaddress($ip4);

        $ip6
            ->setIp('2001:db8::1428:57ab')
            ->setPort(80);
        $vhost6->setIpaddress($ip6);

        return array(
            array($vhost4, '127.0.0.1:80'),
            array($vhost6, '[2001:db8::1428:57ab]:80'),
        );
    }

    /**
     * Test 'is folder root or child' method.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $expect
     *
     * @dataProvider providerTestIsFolderRootOrChild
     */
    public function testIsFolderRootOrChild($haystack, $needle, $expect)
    {
        $vhost = new Vhost();
        $this->assertEquals($expect, $vhost->isFolderRootOrChild($haystack, $needle));
    }

    /**
     * Dataprovider for testIsFolderRootOrChild.
     *
     * @return array
     */
    public function providerTestIsFolderRootOrChild()
    {
        return array(
            array('/home/j/john', '/home/j/john/pictures', true),
            array('/home/j/john', '/tmp', false),
            array('/home/j/john', '/home/j/john', true),
        );
    }

    /**
     * Test getProtectionForDocumentRoot().
     */
    public function testGetProtectionForDocumentRoot()
    {
        $vhost       = new Vhost();
        $domain      = new Domain();
        $protection1 = new Protection($domain);
        $protection2 = new Protection($domain);

        $domain
            ->setPath('/var/www/domain.de')
            ->setWebroot('htdocs');

        $domain
            ->getProtection()
            ->add($protection1);

        $domain
            ->getProtection()
            ->add($protection2);

        $protection2->setPath('htdocs/test');
        $protection1->setPath('htdocs');

        $vhost->setDomain($domain);

        $this->assertEquals($protection1, $vhost->getProtectionForDocumentRoot());

        $domain
            ->getProtection()
            ->removeElement($protection1);

        $this->assertNull($vhost->getProtectionForDocumentRoot());
    }

    /**
     * Test getPathOptionForDocumentRoot().
     */
    public function testGetPathOptionForDocumentRoot()
    {
        $vhost  = new Vhost();
        $domain = new Domain();
        $po1    = new PathOption($domain);
        $po2    = new PathOption($domain);

        $domain
            ->setPath('/var/www/domain.de')
            ->setWebroot('htdocs');

        $domain
            ->getPathoption()
            ->add($po1);

        $domain
            ->getPathoption()
            ->add($po2);

        $po1->setPath('htdocs/test');
        $po2->setPath('htdocs');

        $vhost->setDomain($domain);

        $this->assertEquals($po2, $vhost->getPathOptionForDocumentRoot());

        $domain
            ->getPathoption()
            ->removeElement($po2);

        $this->assertNull($vhost->getPathOptionForDocumentRoot());
    }

    /**
     * Test getDirectoryOptions().
     * No options for docroot.
     */
    public function testGetDirectoryOptionsNoOptionsForDocroot()
    {
        $domain      = new Domain();
        $po1         = new PathOption($domain);
        $po2         = new PathOption($domain);
        $protection1 = new Protection($domain);
        $protection2 = new Protection($domain);

        $domain
            ->setPath('/var/www/domain.de')
            ->setWebroot('htdocs/totallyUnknown');

        $domain
            ->getPathoption()
            ->add($po1);

        $domain
            ->getPathoption()
            ->add($po2);

        $domain
            ->getProtection()
            ->add($protection1);

        $domain
            ->getProtection()
            ->add($protection2);

        $po1->setPath('htdocs/test');
        $po2->setPath('htdocs');

        $protection1->setPath('htdocs');
        $protection2->setPath('htdocs/test');

        // No options for path
        $vhost = new Vhost();
        $vhost->setDomain($domain);
        $this->assertCount(0, $vhost->getDirectoryOptions());
    }

    /**
     * Test getDirectoryOptions().
     */
    public function testGetDirectoryOptions()
    {
        $domain      = new Domain();
        $po1         = new PathOption($domain);
        $po2         = new PathOption($domain);
        $po3         = new PathOption($domain);
        $protection1 = new Protection($domain);
        $protection2 = new Protection($domain);
        $protection3 = new Protection($domain);

        $domain
            ->setPath('/var/www/domain.de')
            ->setWebroot('htdocs/test');

        $domain
            ->getPathoption()
            ->add($po1);

        $domain
            ->getPathoption()
            ->add($po2);

        $domain
            ->getPathoption()
            ->add($po3);

        $domain
            ->getProtection()
            ->add($protection1);

        $domain
            ->getProtection()
            ->add($protection2);

        $domain
            ->getProtection()
            ->add($protection3);

        $po1->setPath('htdocs/test/subfolder');
        $po2->setPath('htdocs');
        $po3->setPath('htdocs/test');

        $protection1->setPath('htdocs');
        $protection2->setPath('htdocs/test/subfolder2');
        $protection3->setPath('htdocs/test');

        $vhost = new Vhost();
        $vhost->setDomain($domain);

        $options = $vhost->getDirectoryOptions();
        $this->assertCount(2, $options);

        $testPo   = false;
        $testProt = false;

        foreach ($options as $optset) {
            if ($optset['pathoption'] !== null) {
                $this->assertEquals($po1, $optset['pathoption']);
                $testPo = true;
            }

            if ($optset['protection'] !== null) {
                $this->assertEquals($protection2, $optset['protection']);
                $testProt = true;
            }
        }

        $this->assertTrue($testPo);
        $this->assertTrue($testProt);
    }
}
