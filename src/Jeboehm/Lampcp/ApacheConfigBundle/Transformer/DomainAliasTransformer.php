<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Transformer;

use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;

/**
 * Class DomainAliasTransformer
 *
 * Get alias domains.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DomainAliasTransformer
{
    /**
     * Get parent subdomain and set some properties
     * from parent domain.
     *
     * @param Subdomain $subdomain
     *
     * @return Subdomain
     */
    public static function transformAliasSubdomain(Subdomain $subdomain)
    {
        if ($subdomain->getParent() !== null) {
            $domain = self::transformAliasDomain($subdomain->getDomain());
            $parent = clone $subdomain->getParent();
            $parent
                ->setDomain($domain)
                ->setSubdomain($subdomain->getSubdomain())
                ->setCertificate($subdomain->getCertificate());

            return $parent;
        }

        return $subdomain;
    }

    /**
     * Get parent domain and set some properties
     * from alias domain.
     *
     * @param Domain $domain
     *
     * @return Domain
     */
    public static function transformAliasDomain(Domain $domain)
    {
        if ($domain->getParent() !== null) {
            $parent = clone $domain->getParent();
            $parent
                ->setDomain($domain->getDomain())
                ->setIpaddress($domain->getIpaddress())
                ->setCertificate($domain->getCertificate());

            return $parent;
        }

        return $domain;
    }
}
