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

use Jeboehm\Lampcp\ApacheConfigBundle\Transformer\DomainAliasTransformer;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;

/**
 * Class DomainAliasTransformerTest
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Tests\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DomainAliasTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test transformAliasDomain().
     *
     * @param Domain $domain
     *
     * @dataProvider domainProvider
     */
    public function testTransformAliasDomain(Domain $domain)
    {
        $new = DomainAliasTransformer::transformAliasDomain($domain);

        if ($domain->getParent() === null) {
            $this->assertEquals($domain->getPath(), $new->getPath());
        } else {
            $this->assertNotEquals($domain->getPath(), $new->getPath());

            $this->assertEquals($domain->getDomain(), $new->getDomain());

            $this->assertEquals(
                $domain
                    ->getCertificate()
                    ->getName(),
                $new
                    ->getCertificate()
                    ->getName()
            );
        }
    }

    /**
     * Test transformAliasSubdomain().
     *
     * @param Subdomain $subdomain
     *
     * @dataProvider subdomainProvider
     */
    public function testTransformAliasSubdomain(Subdomain $subdomain)
    {
        $new = DomainAliasTransformer::transformAliasSubdomain($subdomain);

        if ($subdomain->getParent() === null) {
            $this->assertEquals($subdomain->getPath(), $new->getPath());
        } else {
            $this->assertNotEquals($subdomain->getPath(), $new->getPath());

            $this->assertEquals($subdomain->getSubdomain(), $new->getSubdomain());

            $this->assertEquals(
                $subdomain
                    ->getCertificate()
                    ->getName(),
                $new
                    ->getCertificate()
                    ->getName()
            );
        }
    }

    /**
     * Provide test domains.
     *
     * @return array
     */
    public function domainProvider()
    {
        $certRoot = new Certificate();
        $certRoot->setName('root');

        $certAlias = new Certificate();
        $certAlias->setName('alias');

        $root = new Domain();
        $root
            ->setDomain('root.de')
            ->setPath('/var/www/root')
            ->setCertificate($certRoot);

        $alias = new Domain();
        $alias
            ->setDomain('alias.de')
            ->setPath('/var/www/alias')
            ->setCertificate($certAlias)
            ->setParent($root);

        return array(
            array($root),
            array($alias),
        );
    }

    /**
     * Provide test subdomains.
     */
    public function subdomainProvider()
    {
        $domains   = array();
        $certRoot  = new Certificate();
        $certAlias = new Certificate();

        $certRoot->setName('root');
        $certAlias->setName('alias');

        /*
         * Collect test domains from provider.
         */
        foreach ($this->domainProvider() as $arrDomain) {
            $domains[] = array_pop($arrDomain);
        }

        $alias = new Subdomain(array_pop($domains));
        $root  = new Subdomain(array_pop($domains));

        $alias
            ->setSubdomain('alias')
            ->setPath('/var/www/alias/alias')
            ->setCertificate($certAlias)
            ->setParent($root);

        $root
            ->setSubdomain('root')
            ->setPath('/var/www/root/root')
            ->setCertificate($certRoot);

        return array(
            array($alias),
            array($root),
        );
    }
}
