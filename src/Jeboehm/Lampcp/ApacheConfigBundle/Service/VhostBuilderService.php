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

use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;

class VhostBuilderService extends AbstractBuilderService implements BuilderServiceInterface {
	const _twigVhost         = 'JeboehmLampcpApacheConfigBundle:Apache2:vhost.conf.twig';
	const _twigFcgiStarter   = 'JeboehmLampcpApacheConfigBundle:PHP:php-fcgi-starter.sh.twig';
	const _twigPhpIni        = 'JeboehmLampcpApacheConfigBundle:PHP:php.ini.twig';
	const _domainFileName    = '20_vhost.conf';
	const _domainAliasPrefix = 'www.';

	/**
	 * Get vhost model for domain
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
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
			->setCustom($domain->getCustomconfig())
			->setIpaddress($domain->getIpaddress());

		if(count($domain->getIpaddress()) < 1) {
			$ip = new IpAddress();
			$ip
				->setIp('*')
				->setPort(80);

			$model->setIpaddress(array($ip));
		}

		return $model;
	}

	/**
	 * Get vhost model for subdomain
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Subdomain $subdomain
	 *
	 * @return \Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost
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
	 * @param \Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost $model
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _renderPhpIni(Vhost $model) {
		$phpIniPath   = $this
			->_getConfigService()
			->getParameter('apache.pathphpini');
		$globalConfig = null;

		if(is_readable($phpIniPath)) {
			$globalConfig = file_get_contents($phpIniPath);
		}

		if(empty($globalConfig)) {
			throw new \Exception('Could not read global php.ini');
		}

		return $this->_renderTemplate(self::_twigPhpIni, array(
															  'vhost'  => $model,
															  'global' => $globalConfig,
														 ));
	}

	/**
	 * Generate and save FCGI Starter Script
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @throws \Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 * @return void
	 */
	protected function _generateFcgiStarterForDomain(Domain $domain) {
		$fs       = new Filesystem();
		$filename = $domain->getPath() . '/php-fcgi/php-fcgi-starter.sh';
		$content  = $this->_renderTemplate(self::_twigFcgiStarter, array(
																		'domain' => $domain,
																   ));

		if(!is_writable(dirname($filename))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Generating FCGI-Starter: ' . $filename);
		file_put_contents($filename, $content);

		// Change rights
		$fs->chmod($filename, 0755);

		// Change user & group
		$fs->chown($filename, $domain->getUser()->getName());
		$fs->chgrp($filename, $domain->getUser()->getGroupname());
	}

	/**
	 * Generate and save php.ini
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @throws \Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 */
	protected function _generatePhpIniForDomain(Domain $domain) {
		$fs       = new Filesystem();
		$filename = $domain->getPath() . '/conf/php.ini';

		if(!is_writable(dirname($filename))) {
			throw new CouldNotWriteFileException();
		}

		if(!file_exists($filename)) {
			$this->_getLogger()->info('(VhostBuilderService) Generating php.ini:' . $filename);
			file_put_contents($filename, $this->_renderPhpIni($this->_getVhostModelForDomain($domain)));
		}

		// Change rights
		$fs->chmod($filename, 0440);

		// Change user & group
		$fs->chown($filename, $domain->getUser()->getName());
		$fs->chgrp($filename, $domain->getUser()->getGroupname());
	}

	/**
	 * Save vhost config
	 *
	 * @param string $content
	 *
	 * @throws \Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 * @return void
	 */
	protected function _saveVhostConfig($content) {
		$target = $this
			->_getConfigService()
			->getParameter('apache.pathapache2conf') . '/' . self::_domainFileName;

		if(!is_writable(dirname($target))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Creating new config: ' . $target);
		file_put_contents($target, $content);
	}

	/**
	 * Build all configurations
	 */
	public function buildAll() {
		$domainModels    = array();
		$subdomainModels = array();

		foreach($this->_getAllDomains() as $domain) {
			$domainModels[] = $this->_getVhostModelForDomain($domain);
			$this->_generatePhpIniForDomain($domain);
			$this->_generateFcgiStarterForDomain($domain);
		}

		foreach($this->_getAllSubdomains() as $subdomain) {
			$subdomainModels[] = $this->_getVhostModelForSubdomain($subdomain);
		}

		$content = $this->_renderTemplate(self::_twigVhost, array(
																 'domains' => array_merge($domainModels,
																	 $subdomainModels),
															));

		$this->_saveVhostConfig($content);
	}
}
