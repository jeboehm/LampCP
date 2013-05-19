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

use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;

/**
 * Class DomainTransformer
 *
 * Transform Domain to Vhost.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DomainTransformer
{
    /**
     * Transform domain to vhost.
     *
     * @param Domain    $domain
     * @param Subdomain $subdomain
     *
     * @return Vhost[]
     */
    public static function transformDomain(Domain $domain, Subdomain $subdomain = null)
    {
        /** @var Vhost[] $return */
        $return = array();
        $ips    = $domain->getIpaddress();

        if (($max = count($ips)) < 1) {
            $max = 1;
        }

        for ($i = 0; $i < $max; $i++) {
            $vhost = new Vhost();
            $ip    = null;

            if ($ips->containsKey($i)) {
                /** @var IpAddress $ip */
                $ip = $ips[$i];
            }

            $vhost
                ->setDomain($domain)
                ->setSubdomain($subdomain);

            if ($ip !== null) {
                $vhost->setIpaddress($ip);
            }

            /*
             * Only add if:
             * - Domain has no IP's (so no SSL is required).
             * - IP has no SSL support.
             * - Domain has a Certificate AND IP supports SSL.
             */
            if ($ip === null || $vhost->getSSLEnabled() || !$ip->getHasSsl()) {
                $return[] = $vhost;
            }
        }

        return $return;
    }
}
