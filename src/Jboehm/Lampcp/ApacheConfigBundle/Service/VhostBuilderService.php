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
	 * Get vhost model for domain
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @return Vhost
	 */
	protected function _getVhostModelForDomain(Domain $domain) {
		$model = new Vhost();
		$model
			->setServername($domain->getDomain())
			->setServeralias(self::_domainAliasPrefix . $domain->getDomain())
			->setRoot($domain->getPath())
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
	 * Get vhost model for subdomain
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Subdomain $subdomain
	 *
	 * @return \Jboehm\Lampcp\ApacheConfigBundle\Model\Vhost
	 */
	protected function _getVhostModelForSubdomain(Subdomain $subdomain) {
		$model = $this->_getVhostModelForDomain($subdomain->getDomain());

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
	protected function _renderVhostConfig(Vhost $model) {
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
	 * Generate and save FCGI Starter Script
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 */
	protected function _generateFcgiStarterForDomain(Domain $domain) {
		$filename = $domain->getPath() . '/php-fcgi/php-fcgi-starter.sh';
		file_put_contents($filename, $this->_renderFcgiStarter($domain));

		// Change rights
		chmod($filename, 0755);

		// Change user & group
		chown($filename, $domain->getUser()->getName());
		chgrp($filename, $domain->getUser()->getGroupname());
	}

	/**
	 * Save vhost config
	 *
	 * @param string $filename
	 * @param string $config
	 *
	 * @throws \Jboehm\Lampcp\ApacheConfigBundle\Exception\couldNotWriteFileException
	 * @return void
	 */
	protected function _saveVhostConfig($filename, $config) {
		$target = $this
			->_getSystemConfigService()
			->getParameter('systemconfig.option.apache.config.directory') . '/' . $filename;

		if(!is_writable(dirname($target))) {
			throw new couldNotWriteFileException();
		}

		file_put_contents($target, $config);
	}

	/**
	 * Build domain configuration
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 */
	public function buildDomain(Domain $domain) {
		$filename = $domain->getDomain() . self::_configFileSuffix;
		$config   = $this->_renderVhostConfig($this->_getVhostModelForDomain($domain));

		$this->_saveVhostConfig($filename, $config);
		$this->_generateFcgiStarterForDomain($domain);
	}

	/**
	 * Build subdomain configuration
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Subdomain $subdomain
	 */
	public function buildSubdomain(Subdomain $subdomain) {
		$filename = $subdomain->getFullDomain() . self::_configFileSuffix;
		$config   = $this->_renderVhostConfig($this->_getVhostModelForSubdomain($subdomain));

		$this->_saveVhostConfig($filename, $config);
		$this->_generateFcgiStarterForDomain($subdomain->getDomain());
	}

	/**
	 * Build all configurations
	 */
	public function buildAll() {
		foreach($this->_getAllDomains() as $domain) {
			$this->buildDomain($domain);
		}

		foreach($this->_getAllSubdomains() as $subdomain) {
			$this->buildSubdomain($subdomain);
		}
	}
}
