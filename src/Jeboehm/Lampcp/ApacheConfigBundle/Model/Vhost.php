<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Model;

use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;

/**
 * Class Vhost
 *
 * Holds a virtual host.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Vhost
{
    const _serveralias_prefix_normal   = 'www.';
    const _serveralias_prefix_wildcard = '*.';
    const _php_fcgi_wrapper            = '/php-fcgi/php-fcgi-starter.sh';
    const _log_access                  = '/logs/access.log';
    const _log_error                   = '/logs/error.log';

    /** @var Domain */
    protected $domain;

    /** @var Subdomain */
    protected $subdomain;

    /** @var IpAddress */
    protected $ipaddress;

    /** @var string */
    protected $phpfpmsocket;

    /**
     * VirtualHost
     *
     * @return string
     */
    public function getVhostAddress()
    {
        if ($this
            ->getIpaddress()
            ->isIpv6()
        ) {
            $address = sprintf(
                '[%s]:%s',
                $this
                    ->getIpaddress()
                    ->getIp(),
                $this
                    ->getIpaddress()
                    ->getPort()
            );
        } else {
            $address = sprintf(
                '%s:%s',
                $this
                    ->getIpaddress()
                    ->getIp(),
                $this
                    ->getIpaddress()
                    ->getPort()
            );
        }

        return $address;
    }

    /**
     * ServerName
     *
     * @return string
     */
    public function getServerName()
    {
        if ($this->isSubDomain()) {
            $servername = $this->subdomain->getFullDomain();
        } else {
            $servername = $this->domain->getDomain();
        }

        return $servername;
    }

    /**
     * Is wildcard domain?
     *
     * @return bool
     */
    public function getIsWildcard()
    {
        if ($this->isSubDomain()) {
            $wildcard = $this->subdomain->getIsWildcard();
        } else {
            $wildcard = $this->domain->getIsWildcard();
        }

        return $wildcard;
    }

    /**
     * ServerAlias
     *
     * @return array
     */
    public function getServerAlias()
    {
        if ($this->getIsWildcard()) {
            $serveralias = self::_serveralias_prefix_wildcard;
        } else {
            $serveralias = self::_serveralias_prefix_normal;
        }

        if ($this->isSubDomain()) {
            $serveralias .= $this->subdomain->getFullDomain();
        } else {
            $serveralias .= $this->domain->getDomain();
        }

        return array($serveralias);
    }

    /**
     * SuexecUserGroup
     *
     * @return string
     */
    public function getSuexecUserGroup()
    {
        return sprintf(
            '%s %s',
            $this->domain
                ->getUser()
                ->getName(),
            $this->domain
                ->getUser()
                ->getGroupname()
        );
    }

    /**
     * DocumentRoot
     *
     * @return string
     */
    public function getDocumentRoot()
    {
        if ($this->isSubDomain()) {
            $root = $this->subdomain->getFullPath();
        } else {
            $root = $this->domain->getFullWebrootPath();
        }

        return $root;
    }

    /**
     * RedirectUrl
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        if ($this->isSubDomain()) {
            $url = $this->subdomain->getRedirectUrl();
        } else {
            $url = $this->domain->getRedirectUrl();
        }

        return $url;
    }

    /**
     * Is PHP enabled?
     *
     * @return bool
     */
    public function getPHPEnabled()
    {
        if ($this->getRedirectUrl() != '') {
            return false;
        }

        if ($this->isSubDomain()) {
            $enabled = $this->subdomain->getParsePhp();
        } else {
            $enabled = $this->domain->getParsePhp();
        }

        return $enabled;
    }

    /**
     * True, if all ssl requirements met
     *
     * @return bool
     */
    public function getSSLEnabled()
    {
        if ($this->isSubDomain()) {
            $certificate = $this->subdomain->getCertificate();
        } else {
            $certificate = $this->domain->getCertificate();
        }

        return $this
            ->getIpaddress()
            ->getHasSsl() && $certificate;
    }

    /**
     * Force SSL
     *
     * @return bool
     */
    public function getForceSSL()
    {
        if ($this->isSubDomain()) {
            $force       = $this->subdomain->getForceSsl();
            $certificate = $this->subdomain->getCertificate();
        } else {
            $force       = $this->domain->getForceSsl();
            $certificate = $this->domain->getCertificate();
        }

        if ($certificate && !$this
            ->getIpaddress()
            ->getHasSsl() && $this->getRedirectUrl() == ''
        ) {
            return $force;
        } else {
            return false;
        }
    }

    /**
     * Get Certificate
     *
     * @return Certificate
     */
    public function getCertificate()
    {
        $certificate = null;

        if ($this->getSSLEnabled()) {
            if ($this->isSubDomain()) {
                $certificate = $this->subdomain->getCertificate();
            } else {
                $certificate = $this->domain->getCertificate();
            }
        }

        return $certificate;
    }

    /**
     * CustomConfig
     *
     * @return string
     */
    public function getCustomConfig()
    {
        if ($this->isSubDomain()) {
            $custom = $this->subdomain->getCustomconfig();
        } else {
            $custom = $this->domain->getCustomconfig();
        }

        return $custom;
    }

    /**
     * Pathoption for document root
     *
     * @return PathOption|null
     */
    public function getPathOptionForDocumentRoot()
    {
        $docroot          = $this->getDocumentRoot();
        $returnPathOption = null;
        foreach ($this
                     ->getDomain()
                     ->getPathoption() as $pathoption) {
            /** @var $pathoption PathOption */
            if ($pathoption->getFullPath() == $docroot) {
                $returnPathOption = $pathoption;
            }
        }

        return $returnPathOption;
    }

    /**
     * Protection for document root
     *
     * @return Protection|null
     */
    public function getProtectionForDocumentRoot()
    {
        $docroot          = $this->getDocumentRoot();
        $returnProtection = null;
        foreach ($this
                     ->getDomain()
                     ->getProtection() as $protection) {
            /** @var $protection Protection */
            if ($protection->getFullPath() == $docroot) {
                $returnProtection = $protection;
            }
        }

        return $returnProtection;
    }

    /**
     * Get relevant protections
     *
     * @return Protection[]
     */
    protected function _getProtection()
    {
        $protections = array();
        $docroot     = $this->getDocumentRoot();

        foreach ($this->domain->getProtection() as $protection) {
            /** @var $protection Protection */
            if ($protection->getFullPath() == $docroot) {
                continue;
            }

            $protections[] = $protection;
        }

        return $protections;
    }

    /**
     * Get relevant pathoptions
     *
     * @return PathOption[]
     */
    protected function _getPathOption()
    {
        $pathoptions = array();
        $docroot     = $this->getDocumentRoot();

        foreach ($this->domain->getPathoption() as $pathoption) {
            /** @var $pathoption PathOption */
            if ($pathoption->getFullPath() == $docroot) {
                continue;
            }

            $pathoptions[] = $pathoption;
        }

        return $pathoptions;
    }

    /**
     * Get Directory Options
     *
     * @return array
     */
    public function getDirectoryOptions()
    {
        $options = array();
        $ordered = array();

        foreach ($this->_getPathOption() as $pathoption) {
            $path = $pathoption->getFullPath();

            if (!$this->isFolderRootOrChild($this->getDocumentRoot(), $path)) {
                continue;
            }

            if (!isset($options[$path])) {
                $options[$path] = array(
                    'pathoption' => null,
                    'protection' => null,
                );
            }

            $options[$path]['pathoption'] = $pathoption;
        }

        foreach ($this->_getProtection() as $protection) {
            $path = $protection->getFullPath();

            if (!$this->isFolderRootOrChild($this->getDocumentRoot(), $path)) {
                continue;
            }

            if (!isset($options[$path])) {
                $options[$path] = array(
                    'pathoption' => null,
                    'protection' => null,
                );
            }

            $options[$path]['protection'] = $protection;
        }

        foreach ($options as $path => $option) {
            $ordered[] = array(
                'path'       => $path,
                'pathoption' => $option['pathoption'],
                'protection' => $option['protection'],
            );
        }

        return $ordered;
    }

    /**
     * Get IP Address
     *
     * @return IpAddress
     */
    public function getIpaddress()
    {
        if ($this->ipaddress) {
            $ipaddress = $this->ipaddress;
        } else {
            $ipaddress = new IpAddress();
            $ipaddress
                ->setIp('*')
                ->setPort(80)
                ->setHasSsl(false);
        }

        return $ipaddress;
    }

    /**
     * Set IP Address
     *
     * @param IpAddress $ipaddress
     *
     * @return Vhost
     */
    public function setIpaddress(IpAddress $ipaddress)
    {
        $this->ipaddress = $ipaddress;

        return $this;
    }

    /**
     * AccessLog
     *
     * @return string
     */
    public function getAccessLog()
    {
        return $this->domain->getPath() . self::_log_access;
    }

    /**
     * ErrorLog
     *
     * @return string
     */
    public function getErrorLog()
    {
        return $this->domain->getPath() . self::_log_error;
    }

    /**
     * Set domain
     *
     * @param Domain $domain
     *
     * @return Vhost
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set subdomain
     *
     * @param Subdomain $subdomain
     *
     * @return Vhost
     */
    public function setSubdomain($subdomain)
    {
        if ($subdomain !== null) {
            $this->subdomain = $subdomain;
        }

        return $this;
    }

    /**
     * Get subdomain
     *
     * @return Subdomain
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Checks, if $needle is in $haystack or $needle is the same as $haystack
     *
     * Eg.:
     *     ("/home/j/john", "/home/j/john/pictures")   == true
     *     ("/home/j/john", "/bin")                    == false
     *     ("/home/j/john", "/home/j/john")            == true
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public function isFolderRootOrChild($haystack, $needle)
    {
        $result = false;

        if ($haystack === $needle) {
            $result = true;
        } else {
            if (strlen($needle) > strlen($haystack)) {
                if (substr($needle, 0, strlen($haystack)) === $haystack) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Set PHP-FPM Socket.
     *
     * @param string $phpfpmsocket
     *
     * @return Vhost
     */
    public function setPhpFpmSocket($phpfpmsocket)
    {
        $this->phpfpmsocket = $phpfpmsocket;

        return $this;
    }

    /**
     * Get PHP-FPM Socket.
     *
     * @return string
     */
    public function getPhpFpmSocket()
    {
        return $this->phpfpmsocket;
    }

    /**
     * Get FastCGIExternalServer directory.
     *
     * @return string
     */
    public function getFcgiExternalServerDirectory()
    {
        return sprintf('%s/%s', $this->getDocumentRoot(), $this->getFcgiExternalServerDirectoryName());
    }

    /**
     * Get FastCGIExternalServer directory name.
     *
     * @return string
     */
    public function getFcgiExternalServerDirectoryName()
    {
        return 'php-fpm';
    }

    /**
     * True, if this vhost got a subdomain.
     *
     * @return bool
     */
    public function isSubdomain()
    {
        if ($this->getSubdomain() !== null) {
            return true;
        }

        return false;
    }
}
