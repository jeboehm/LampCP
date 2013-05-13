<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Service;

use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\EmptyConfigPathException;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\ApacheConfigBundle\Transformer\DomainAliasTransformer;
use Jeboehm\Lampcp\ApacheConfigBundle\Transformer\DomainTransformer;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\PhpFpmBundle\Service\ConfigBuilderService as PhpFpmConfigBuilder;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class VhostBuilderService
 *
 * Builds the Apache2 configuration.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderService
{
    const vhostConfigTemplate = 'JeboehmLampcpApacheConfigBundle:Apache2:vhost.conf.twig';
    const vhostConfigFilename = '20_vhost.conf';
    /** @var PhpFpmConfigBuilder */
    private $_phpFpmConfigBuilder;
    /** @var Domain[] */
    private $_domains;
    /** @var Subdomain[] */
    private $_subdomains;
    /** @var Vhost[] */
    private $_vhosts;
    /** @var string */
    private $_configdir;
    /** @var TwigEngine */
    private $_twigEngine;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_domains    = array();
        $this->_subdomains = array();
        $this->_vhosts     = array();
    }

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
     * Get domains.
     *
     * @return Domain[]
     */
    public function getDomains()
    {
        return $this->_domains;
    }

    /**
     * Set domains.
     *
     * @param array $domains
     *
     * @return $this
     */
    public function setDomains(array $domains)
    {
        $this->_domains = $domains;

        return $this;
    }

    /**
     * Add vhost.
     *
     * @param Vhost $vhost
     *
     * @return $this
     */
    public function addVhost(Vhost $vhost)
    {
        $this->_vhosts[] = $vhost;

        return $this;
    }

    /**
     * Get subdomains.
     *
     * @return Subdomain[]
     */
    public function getSubdomains()
    {
        return $this->_subdomains;
    }

    /**
     * Set subdomains.
     *
     * @param array $subdomains
     *
     * @return $this
     */
    public function setSubdomains(array $subdomains)
    {
        $this->_subdomains = $subdomains;

        return $this;
    }

    /**
     * Order vhosts.
     * Non-wildcard vhosts at first.
     *
     * @return array
     */
    public function sortVhosts()
    {
        $this->setVhosts(array_merge($this->_getNonWildcardVhosts(), $this->_getWildcardVhosts()));
    }

    /**
     * Get all non wildcard vhosts.
     *
     * @return array
     */
    protected function _getNonWildcardVhosts()
    {
        $nonWildcard = array();

        foreach ($this->getVhosts() as $vhost) {
            if (!$vhost->getIsWildcard()) {
                $nonWildcard[] = $vhost;
            }
        }

        return $nonWildcard;
    }

    /**
     * Get vhosts.
     *
     * @return Vhost[]
     */
    public function getVhosts()
    {
        return $this->_vhosts;
    }

    /**
     * Set vhosts.
     *
     * @param array $vhosts
     *
     * @return $this
     */
    public function setVhosts(array $vhosts)
    {
        $this->_vhosts = $vhosts;

        return $this;
    }

    /**
     * Get all wildcard vhosts.
     *
     * @return array
     */
    protected function _getWildcardVhosts()
    {
        $wildcard = array();

        foreach ($this->getVhosts() as $vhost) {
            if ($vhost->getIsWildcard()) {
                $wildcard[] = $vhost;
            }
        }

        return $wildcard;
    }

    /**
     * Builds the configuration with the given
     * vhosts.
     */
    public function buildConfiguration()
    {
        $content = $this->renderConfiguration();
        $this->saveConfiguration($content);
    }

    /**
     * Render configuration.
     *
     * @return string
     */
    public function renderConfiguration()
    {
        $parameters = array(
            'vhosts' => $this->getVhosts(),
            'ips'    => $this->getIpAddresses(),
        );

        $content = $this
            ->getTwigEngine()
            ->render(self::vhostConfigTemplate, $parameters);

        return $content;
    }

    /**
     * Get the ip addresses of all vhosts.
     *
     * @return array
     */
    public function getIpAddresses()
    {
        $ips = array();

        foreach ($this->getVhosts() as $vhost) {
            $ip = $vhost->getIpaddress();

            if (!in_array($ip, $ips)) {
                $ips[] = $ip;
            }
        }

        return $ips;
    }

    /**
     * Get TwigEngine.
     *
     * @return TwigEngine
     */
    public function getTwigEngine()
    {
        return $this->_twigEngine;
    }

    /**
     * Set TwigEngine.
     *
     * @param TwigEngine $twigEngine
     *
     * @return VhostBuilderService
     */
    public function setTwigEngine($twigEngine)
    {
        $this->_twigEngine = $twigEngine;

        return $this;
    }

    /**
     * Save vhost config.
     *
     * @param string $content
     *
     * @throws CouldNotWriteFileException
     * @return bool
     */
    public function saveConfiguration($content)
    {
        $fs       = new Filesystem();
        $filepath = sprintf('%s/%s', $this->getConfigdir(), self::vhostConfigFilename);
        $content  = str_replace('  ', '', $content);
        $content  = str_replace(PHP_EOL . PHP_EOL, PHP_EOL, $content);

        if (!$fs->exists($filepath)) {
            $fs->touch($filepath);
        }

        file_put_contents($filepath, $content);

        return true;
    }

    /**
     * Get config directory.
     *
     * @throws EmptyConfigPathException
     * @return string
     */
    public function getConfigdir()
    {
        if (empty($this->_configdir)) {
            throw new EmptyConfigPathException();
        }

        return $this->_configdir;
    }

    /**
     * Set config directory.
     *
     * @param string $configdir
     *
     * @throws EmptyConfigPathException
     * @return VhostBuilderService
     */
    public function setConfigdir($configdir)
    {
        $fs = new Filesystem();

        if (!$fs->exists($configdir)) {
            throw new EmptyConfigPathException();
        }

        $this->_configdir = $configdir;

        return $this;
    }

    /**
     * Sets the PHP-Socket to all vhosts,
     * which are PHP-enabled.
     */
    public function setPhpSocketToVhosts()
    {
        foreach ($this->getVhosts() as $vhost) {
            if ($vhost->getPHPEnabled()) {
                $vhost->setPhpFpmSocket($this->getPhpSocket($vhost->getDomain()));
            }
        }
    }

    /**
     * Get PHP-FPM Socket.
     *
     * @param Domain $domain
     *
     * @return string
     */
    public function getPhpSocket(Domain $domain)
    {
        $socket = $this
            ->getPhpFpmConfigBuilder()
            ->getPoolCreator($domain->getUser())
            ->getSocketPath();

        return $socket;
    }

    /**
     * Get PHP-FPM ConfigBuilder.
     *
     * @return PhpFpmConfigBuilder
     */
    public function getPhpFpmConfigBuilder()
    {
        return $this->_phpFpmConfigBuilder;
    }

    /**
     * Set PHP-FPM ConfigBuilder.
     *
     * @param PhpFpmConfigBuilder $cb
     *
     * @return $this
     */
    public function setPhpFpmConfigBuilder(PhpFpmConfigBuilder $cb)
    {
        $this->_phpFpmConfigBuilder = $cb;

        return $this;
    }
}
