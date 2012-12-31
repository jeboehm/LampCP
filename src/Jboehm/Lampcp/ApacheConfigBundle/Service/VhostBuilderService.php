<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ApacheConfigBundle\Service;

use Jboehm\Lampcp\CoreBundle\Entity\Domain;
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jboehm\Lampcp\ApacheConfigBundle\Exception\couldNotWriteFileException;

class VhostBuilderService extends AbstractBuilderService {
	const _twigVhost         = 'JboehmLampcpApacheConfigBundle:Default:vhost.conf.twig';
	const _twigFcgiStarter   = 'JboehmLampcpApacheConfigBundle:Default:php-fcgi-starter.sh.twig';
	const _configFileSuffix  = '.conf';
	const _domainAliasPrefix = 'www.';

	/**
	 * Get domain model for domain
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @return Vhost
	 */
	protected function _getDomainConfig(Domain $domain) {
		$model = new Vhost();
		$model
			->setServername($domain->getDomain())
			->setServeralias(self::_domainAliasPrefix . $domain->getDomain())
			->setDocroot($domain->getFullWebrootPath())
			->setSuexecuser($domain->getUser()->getName())
			->setSuexecgroup($domain->getUser()->getGroupname())
			->setFcgiwrapper($domain->getPath() . '/php-fcgi/php-fcgi-starter.sh')
			->setCustomlog($domain->getPath() . '/logs/access.log')
			->setErrorlog($domain->getPath() . '/logs/error.log')
			->setCustom($domain->getCustomconfig());

		return $model;
	}

	/**
	 * Get domain model for subdomain
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Subdomain $subdomain
	 *
	 * @return \Jboehm\Lampcp\ApacheConfigBundle\Model\Vhost
	 */
	protected function _getSubdomainConfig(Subdomain $subdomain) {
		$model = $this->_getDomainConfig($subdomain->getDomain());

		$model
			->setServername($subdomain->getFullDomain())
			->setServeralias(self::_domainAliasPrefix . $subdomain->getFullDomain())
			->setDocroot($subdomain->getFullPath())
			->setCustom($subdomain->getCustomconfig());

		return $model;
	}

	/**
	 * @param \Jboehm\Lampcp\ApacheConfigBundle\Model\Vhost $model
	 *
	 * @return string
	 */
	protected function _renderConfig(Vhost $model) {
		return $this->_getTemplating()->render(self::_twigVhost, array('vhost' => $model));
	}

	/**
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @return string
	 */
	protected function _renderFcgiStarter(Domain $domain) {
		return $this->_getTemplating()->render(self::_twigFcgiStarter, array('domain' => $domain));
	}

	/**
	 * Gets rendered config files for all domains and subdomains
	 *
	 * @return array
	 */
	protected function _getAllConfigFiles() {
		$config = array();

		foreach($this->_getAllDomains() as $domain) {
			$filename          = $domain->getDomain() . self::_configFileSuffix;
			$config[$filename] = $this->_renderConfig($this->_getDomainConfig($domain));
		}

		foreach($this->_getAllSubdomains() as $subdomain) {
			$filename          = $subdomain->getFullDomain() . self::_configFileSuffix;
			$config[$filename] = $this->_renderConfig($this->_getSubdomainConfig($subdomain));
		}

		return $config;
	}

	/**
	 * Generate and save fcgi starter files
	 */
	protected function _writeFcgiStarter() {
		foreach($this->_getAllDomains() as $domain) {
			$filename = $domain->getPath() . '/php-fcgi/php-fcgi-starter.sh';
			file_put_contents($filename, $this->_renderFcgiStarter($domain));
		}
	}

	/**
	 * Write config files
	 */
	public function writeConfigFiles() {
		foreach($this->_getAllConfigFiles() as $filename => $content) {
			$target = $this
				->_getSystemConfigService()
				->getParameter('systemconfig.option.apache.config.directory') . '/' . $filename;

			if(!is_writable(dirname($target))) {
				throw new couldNotWriteFileException();
			}

			file_put_contents($target, $content);
		}

		$this->_writeFcgiStarter();
	}
}
