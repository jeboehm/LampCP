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

use Symfony\Component\Filesystem\Filesystem;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\GlobalPhpIniNotFoundException;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;

/**
 * Class VhostBuilderService
 *
 * Builds the Apache2 configuration.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class VhostBuilderService extends AbstractBuilderService implements BuilderServiceInterface {
    const _twigVhost       = 'JeboehmLampcpApacheConfigBundle:Apache2:vhost.conf.twig';
    const _twigFcgiStarter = 'JeboehmLampcpApacheConfigBundle:PHP:php-fcgi-starter.sh.twig';
    const _twigPhpIni      = 'JeboehmLampcpApacheConfigBundle:PHP:php.ini.twig';
    const _domainFileName  = '20_vhost.conf';

    /**
     * Render php.ini.
     *
     * @param Domain $domain
     *
     * @throws GlobalPhpIniNotFoundException
     * @return string
     */
    protected function _renderPhpIni(Domain $domain) {
        $phpIniPath   = $this
            ->_getConfigService()
            ->getParameter('apache.pathphpini');
        $globalConfig = null;

        if (is_readable($phpIniPath)) {
            $globalConfig = file_get_contents($phpIniPath);
        }

        if (empty($globalConfig)) {
            throw new GlobalPhpIniNotFoundException();
        }

        return $this->_renderTemplate(self::_twigPhpIni, array(
                                                              'domain' => $domain,
                                                              'global' => $globalConfig,
                                                         ));
    }

    /**
     * Generate and save FCGI Starter Script.
     *
     * @param Domain $domain
     *
     * @throws CouldNotWriteFileException
     * @return void
     */
    protected function _generateFcgiStarterForDomain(Domain $domain) {
        $fs       = new Filesystem();
        $filename = $domain->getPath() . '/php-fcgi/php-fcgi-starter.sh';

        if (!is_writable(dirname($filename))) {
            throw new CouldNotWriteFileException();
        }

        if (!$fs->exists($filename)) {
            $content = $this->_renderTemplate(self::_twigFcgiStarter, array(
                                                                           'domain' => $domain,
                                                                      ));

            file_put_contents($filename, $content);
        }

        // Change rights
        $fs->chmod($filename, 0755);

        // Change user & group
        $fs->chown($filename, $domain
            ->getUser()
            ->getName());
        $fs->chgrp($filename, $domain
            ->getUser()
            ->getGroupname());
    }

    /**
     * Generate and save php.ini.
     *
     * @param Domain $domain
     *
     * @throws CouldNotWriteFileException
     */
    protected function _generatePhpIniForDomain(Domain $domain) {
        $fs       = new Filesystem();
        $filename = $domain->getPath() . '/conf/php.ini';

        if (!is_writable(dirname($filename))) {
            throw new CouldNotWriteFileException();
        }

        if (!$fs->exists($filename)) {
            file_put_contents($filename, $this->_renderPhpIni($domain));
        }

        // Change rights
        $fs->chmod($filename, 0440);

        // Change user & group
        $fs->chown($filename, $domain
            ->getUser()
            ->getName());
        $fs->chgrp($filename, $domain
            ->getUser()
            ->getGroupname());
    }

    /**
     * Save vhost config.
     *
     * @param string $content
     *
     * @throws CouldNotWriteFileException
     * @return void
     */
    protected function _saveVhostConfig($content) {
        $target = $this
            ->_getConfigService()
            ->getParameter('apache.pathapache2conf') . '/' . self::_domainFileName;

        $content = str_replace('  ', '', $content);
        $content = str_replace(PHP_EOL . PHP_EOL, PHP_EOL, $content);

        if (!is_writable(dirname($target))) {
            throw new CouldNotWriteFileException();
        }

        file_put_contents($target, $content);
    }

    /**
     * Get all IPs
     *
     * @return IpAddress[]
     */
    protected function _getAllIpAddresses() {
        /** @var $ips IpAddress[] */
        $ips = $this
            ->_getDoctrine()
            ->getRepository('JeboehmLampcpCoreBundle:IpAddress')
            ->findAll();

        return $ips;
    }

    /**
     * Get Vhost Models.
     *
     * @return Vhost[]
     */
    protected function _getVhostModels() {
        /** @var $models Vhost[] */
        $models = array();

        foreach ($this->_getAllDomains() as $domain) {
            /**
             * Get alias
             */
            if ($domain->getParent() !== null) {
                $domain = $this->_getAliasDomain($domain);
            }

            if ($domain
                ->getIpaddress()
                ->count() > 0
            ) {
                foreach ($domain->getIpaddress() as $ipaddress) {
                    /** @var $ipaddress IpAddress */
                    $vhost = $this->_getVhost();
                    $vhost
                        ->setDomain($domain)
                        ->setIpaddress($ipaddress);

                    if ($vhost->getSSLEnabled() || !$ipaddress->getHasSsl()) {
                        $models[] = $vhost;
                    }
                }
            } else {
                $vhost = $this->_getVhost();
                $vhost->setDomain($domain);
                $models[] = $vhost;
            }

            $this->_generatePhpIniForDomain($domain);
            $this->_generateFcgiStarterForDomain($domain);
        }

        foreach ($this->_getAllSubdomains() as $subdomain) {
            /**
             * Get alias
             */
            if ($subdomain->getParent() !== null) {
                $subdomain = $this->_getAliasSubdomain($subdomain);
            }

            if ($subdomain
                ->getDomain()
                ->getIpaddress()
                ->count() > 0
            ) {
                foreach ($subdomain
                             ->getDomain()
                             ->getIpaddress() as $ipaddress) {
                    /** @var $ipaddress IpAddress */
                    $vhost = $this->_getVhost();
                    $vhost
                        ->setDomain($subdomain->getDomain())
                        ->setSubdomain($subdomain)
                        ->setIpaddress($ipaddress);

                    if ($vhost->getSSLEnabled() || !$ipaddress->getHasSsl()) {
                        $models[] = $vhost;
                    }
                }
            } else {
                $vhost = $this->_getVhost();
                $vhost
                    ->setDomain($subdomain->getDomain())
                    ->setSubdomain($subdomain);
                $models[] = $vhost;
            }
        }

        $models = $this->_orderVhosts($models);

        return $models;
    }

    /**
     * Build all configurations.
     */
    public function buildAll() {
        $content = $this->_renderTemplate(self::_twigVhost, array(
                                                                 'vhosts' => $this->_getVhostModels(),
                                                                 'ips'    => $this->_getAllIpAddresses(),
                                                            ));

        $this->_saveVhostConfig($content);
    }

    /**
     * Order vhost models by wildcard.
     *
     * @param array $vhosts
     *
     * @return array
     */
    protected function _orderVhosts(array $vhosts) {
        $nonWc = array();
        $wc    = array();

        foreach ($vhosts as $vhost) {
            /** @var $vhost Vhost */
            if ($vhost->getIsWildcard()) {
                $wc[] = $vhost;
            } else {
                $nonWc[] = $vhost;
            }
        }

        return array_merge($nonWc, $wc);
    }

    /**
     * Get Vhost Model.
     *
     * @return Vhost
     */
    protected function _getVhost() {
        return new Vhost();
    }

    /**
     * Get parent domain and set some properties from alias-domain.
     *
     * @param Domain $domain
     *
     * @return Domain
     */
    protected function _getAliasDomain(Domain $domain) {
        $parent = clone $domain->getParent();
        $parent
            ->setDomain($domain->getDomain())
            ->setIpaddress($domain->getIpaddress())
            ->setCertificate($domain->getCertificate());

        return $parent;
    }

    /**
     * Get parent subdomain and set some properties from alias-subdomain.
     *
     * @param Subdomain $subdomain
     *
     * @return Subdomain
     */
    protected function _getAliasSubdomain(Subdomain $subdomain) {
        $domain = clone $subdomain
            ->getParent()
            ->getDomain();

        $domain
            ->setDomain($subdomain
                ->getDomain()
                ->getDomain())
            ->setIpaddress($subdomain
                ->getDomain()
                ->getIpaddress())
            ->setCertificate($subdomain
                ->getDomain()
                ->getCertificate());

        $parent = clone $subdomain->getParent();
        $parent
            ->setDomain($domain)
            ->setSubdomain($subdomain->getSubdomain())
            ->setCertificate($subdomain->getCertificate());

        return $parent;
    }
}
