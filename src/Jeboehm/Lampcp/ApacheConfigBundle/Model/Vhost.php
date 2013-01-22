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

use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;

class Vhost {
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

	/** @var boolean */
	protected $isSubDomain;

	/**
	 * VirtualHost
	 *
	 * @return string
	 */
	public function getVhostAddress() {
		if($this->getIpaddress()->isIpv6()) {
			$address = sprintf('[%s]:%s', $this->getIpaddress()->getIp(), $this->getIpaddress()->getPort());
		} else {
			$address = sprintf('%s:%s', $this->getIpaddress()->getIp(), $this->getIpaddress()->getPort());
		}

		return $address;
	}

	/**
	 * ServerName
	 *
	 * @return string
	 */
	public function getServerName() {
		if($this->isSubDomain) {
			$servername = $this->subdomain->getFullDomain();
		} else {
			$servername = $this->domain->getDomain();
		}

		return $servername;
	}

	/**
	 * ServerAlias
	 *
	 * @return array
	 */
	public function getServerAlias() {
		if($this->isSubDomain) {
			if($this->subdomain->getIsWildcard()) {
				$serveralias = self::_serveralias_prefix_wildcard . $this->subdomain->getFullDomain();
			} else {
				$serveralias = self::_serveralias_prefix_normal . $this->domain->getDomain();
			}
		} else {
			if($this->domain->getIsWildcard()) {
				$serveralias = self::_serveralias_prefix_wildcard;
			} else {
				$serveralias = self::_serveralias_prefix_normal;
			}

			$serveralias .= $this->domain->getDomain();
		}

		return array($serveralias);
	}

	/**
	 * SuexecUserGroup
	 *
	 * @return string
	 */
	public function getSuexecUserGroup() {
		return sprintf(
			'%s %s',
			$this->domain->getUser()->getName(),
			$this->domain->getUser()->getGroupname()
		);
	}

	/**
	 * DocumentRoot
	 *
	 * @return string
	 */
	public function getDocumentRoot() {
		if($this->isSubDomain) {
			$root = $this->subdomain->getFullPath();
		} else {
			$root = $this->domain->getFullWebrootPath();
		}

		return $root;
	}

	/**
	 * Is PHP enabled?
	 *
	 * @return bool
	 */
	public function getPHPEnabled() {
		if($this->isSubDomain) {
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
	public function getSSLEnabled() {
		if($this->isSubDomain) {
			$certificate = $this->subdomain->getCertificate();
		} else {
			$certificate = $this->domain->getCertificate();
		}

		return $this->getIpaddress()->getHasSsl() && $certificate;
	}

	/**
	 * Get Certificate
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Certificate
	 */
	public function getCertificate() {
		$certificate = null;

		if($this->getSSLEnabled()) {
			if($this->isSubDomain) {
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
	public function getCustomConfig() {
		if($this->isSubDomain) {
			$custom = $this->subdomain->getCustomconfig();
		} else {
			$custom = $this->domain->getCustomconfig();
		}

		return $custom;
	}

	/**
	 * FcgiWrapper
	 *
	 * @return string
	 */
	public function getFcgiWrapper() {
		$wrapper = '';

		if($this->getPHPEnabled()) {
			$wrapper = $this->domain->getPath() . self::_php_fcgi_wrapper;
		}

		return $wrapper;
	}

	/**
	 * Pathoption for document root
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\PathOption|null
	 */
	public function getPathOptionForDocumentRoot() {
		$docroot          = $this->getDocumentRoot();
		$returnPathOption = null;
		foreach($this->getDomain()->getPathoption() as $pathoption) {
			/** @var $pathoption PathOption */
			if($pathoption->getFullPath() == $docroot) {
				$returnPathOption = $pathoption;
			}
		}

		return $returnPathOption;
	}

	/**
	 * Protection for document root
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Protection|null
	 */
	public function getProtectionForDocumentRoot() {
		$docroot          = $this->getDocumentRoot();
		$returnProtection = null;
		foreach($this->getDomain()->getProtection() as $protection) {
			/** @var $protection Protection */
			if($protection->getFullPath() == $docroot) {
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
	protected function _getProtection() {
		$protections = array();
		$docroot     = $this->getDocumentRoot();

		foreach($this->domain->getProtection() as $protection) {
			/** @var $protection Protection */
			if($protection->getFullPath() == $docroot) {
				continue;
			}

			if(substr($protection->getFullPath(), 0, strlen($docroot)) == $docroot) {
				$protections[] = $protection;
			}
		}

		return $protections;
	}

	/**
	 * Get relevant pathoptions
	 *
	 * @return PathOption[]
	 */
	protected function _getPathOption() {
		$pathoptions = array();
		$docroot     = $this->getDocumentRoot();

		foreach($this->domain->getPathoption() as $pathoption) {
			/** @var $pathoption PathOption */
			if($pathoption->getFullPath() == $docroot) {
				continue;
			}

			if(substr($pathoption->getFullPath(), 0, strlen($docroot)) == $docroot) {
				$pathoptions[] = $pathoption;
			}
		}

		return $pathoptions;
	}

	/**
	 * Get Directory Options
	 *
	 * @return array
	 */
	public function getDirectoryOptions() {
		$options = array();

		foreach($this->_getPathOption() as $pathoption) {
			$path = $pathoption->getFullPath();

			if(!is_array($options[$path])) {
				$options[$path] = array(
					'pathoption' => null,
					'protection' => null,
				);
			}

			$options[$path]['pathoption'] = $pathoption;
		}

		foreach($this->_getProtection() as $protection) {
			$path = $protection->getFullPath();

			if(isset($options[$path])) {
				if(!is_array($options[$path])) {
					$options[$path] = array(
						'pathoption' => null,
						'protection' => null,
					);
				}
			}

			$options[$path]['protection'] = $protection;
		}

		return $options;
	}

	/**
	 * Get IP Address
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\IpAddress
	 */
	public function getIpaddress() {
		if($this->ipaddress) {
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
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\IpAddress $ipaddress
	 *
	 * @return Vhost
	 */
	public function setIpaddress(IpAddress $ipaddress) {
		$this->ipaddress = $ipaddress;

		return $this;
	}

	/**
	 * AccessLog
	 *
	 * @return string
	 */
	public function getAccessLog() {
		return $this->domain->getPath() . self::_log_access;
	}

	/**
	 * ErrorLog
	 *
	 * @return string
	 */
	public function getErrorLog() {
		return $this->domain->getPath() . self::_log_error;
	}

	/**
	 * Set domain
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @return Vhost
	 */
	public function setDomain($domain) {
		$this->domain = $domain;

		return $this;
	}

	/**
	 * Get domain
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set subdomain
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Subdomain $subdomain
	 *
	 * @return Vhost
	 */
	public function setSubdomain($subdomain) {
		$this->subdomain   = $subdomain;
		$this->isSubDomain = true;

		return $this;
	}

	/**
	 * Get subdomain
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Subdomain
	 */
	public function getSubdomain() {
		return $this->subdomain;
	}
}
