<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\ApacheConfigBundle\Transformer\DomainTransformer;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;

/**
 * Class DomainTransformerTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DomainTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Provide test-data.
     */
    public function dataProvider()
    {
        $certificate = new Certificate();

        $domainWithoutIp = new Domain();
        $domainWithoutIp->setDomain('withoutIp.de');

        $ipaddressWithSSL = new IpAddress();
        $ipaddressWithSSL->setHasSsl(true);

        $ipaddressWithoutSSL = new IpAddress();
        $ipaddressWithoutSSL->setHasSsl(false);

        $domainWithIPs = new Domain();
        $domainWithIPs
            ->setDomain('withIp.de')
            ->setIpaddress(new ArrayCollection(array($ipaddressWithoutSSL, $ipaddressWithSSL)))
            ->setCertificate($certificate);

        $subdomainWithSSL = new Subdomain($domainWithIPs);
        $subdomainWithSSL
            ->setCertificate($certificate)
            ->setSubdomain('withssl');

        $subdomainWithSSL2 = new Subdomain($domainWithoutIp);
        $subdomainWithSSL2
            ->setCertificate($certificate)
            ->setSubdomain('withssl');

        $subdomainWithoutSSL = new Subdomain($domainWithIPs);
        $subdomainWithoutSSL->setSubdomain('withoutssl');

        $subdomainWithoutSSL2 = new Subdomain($domainWithoutIp);
        $subdomainWithoutSSL2->setSubdomain('withoutssl');

        return array(
            array($domainWithIPs, $subdomainWithoutSSL),
            array($domainWithIPs, $subdomainWithSSL),
            array($domainWithoutIp, $subdomainWithSSL2),
            array($domainWithoutIp, $subdomainWithoutSSL2),
            array($domainWithIPs),
            array($domainWithoutIp),
        );
    }

    /**
     * Test transformDomain().
     *
     * @param Domain    $domain
     * @param Subdomain $subdomain
     *
     * @dataProvider dataProvider
     */
    public function testTransformDomain(Domain $domain, Subdomain $subdomain = null)
    {
        $vhosts = DomainTransformer::transformDomain($domain, $subdomain);

        foreach ($vhosts as $vhost) {
            /** @var Vhost $vhost */
            $this->assertEquals($domain, $vhost->getDomain());
            $this->assertEquals($subdomain, $vhost->getSubdomain());
        }
    }
}
