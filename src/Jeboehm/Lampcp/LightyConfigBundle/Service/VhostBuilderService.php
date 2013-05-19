<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\Service;

use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService as ParentVhostBuilder;
use Jeboehm\Lampcp\ApacheConfigBundle\Transformer\DomainAliasTransformer;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\LightyConfigBundle\Transformer\DomainTransformer;

/**
 * Class VhostBuilderService
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderService extends ParentVhostBuilder
{
    const vhostConfigTemplate = 'JeboehmLampcpLightyConfigBundle:Lighttpd:vhost.conf.twig';

    /**
     * Collect vhost models.
     */
    public function collectVhostModels()
    {
        /*
         * Add domains.
         */
        foreach ($this->getDomains() as $domain) {
            $domain = DomainAliasTransformer::transformAliasDomain($domain);
            $vhosts = DomainTransformer::transformDomain($domain);

            foreach ($vhosts as $vhost) {
                $this->addVhost($vhost);
            }
        }

        /*
         * Add subdomains.
         */
        foreach ($this->getSubdomains() as $subdomain) {
            $subdomain = DomainAliasTransformer::transformAliasSubdomain($subdomain);
            $vhosts    = DomainTransformer::transformDomain($subdomain->getDomain(), $subdomain);

            foreach ($vhosts as $vhost) {
                $this->addVhost($vhost);
            }
        }

        $this->sortVhosts();
    }

    /**
     * Render configuration.
     *
     * @return string
     */
    public function renderConfiguration()
    {
        $parameters = array(
            'vhosts'      => $this->getVhosts(),
            'ips'         => $this->getIpAddresses(),
            'defaultcert' => $this->getSingleCertificateWithDomainsAssigned(),
        );

        $content = $this
            ->getTwigEngine()
            ->render(self::vhostConfigTemplate, $parameters);

        return $content;
    }

    /**
     * Get single certificate with domain / subdomain set
     *
     * @return Certificate
     */
    public function getSingleCertificateWithDomainsAssigned()
    {
        foreach ($this->getVhosts() as $vhost) {
            if ($vhost->getCertificate() !== null) {
                return $vhost->getCertificate();
            }
        }

        return null;
    }
}
