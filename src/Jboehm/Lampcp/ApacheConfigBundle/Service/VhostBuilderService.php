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
use Jboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;

class VhostBuilderService extends AbstractBuilderService {
	const _twigVhost         = 'JboehmLampcpApacheConfigBundle:Default:vhost.conf.twig';
	const _twigFcgiStarter   = 'JboehmLampcpApacheConfigBundle:Default:php-fcgi-starter.sh.twig';
	const _twigPhpIni        = 'JboehmLampcpApacheConfigBundle:Default:php.ini.twig';
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
	 * @param \Jboehm\Lampcp\ApacheConfigBundle\Model\Vhost $model
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _renderPhpIni(Vhost $model) {
		$phpIniPath   = $this
			->_getSystemConfigService()
			->getParameter('systemconfig.option.apache.config.php.ini');
		$globalConfig = null;

		if(is_readable($phpIniPath)) {
			$globalConfig = file_get_contents($phpIniPath);
		}

		if(empty($globalConfig)) {
			throw new \Exception('Could not read global php.ini');
		}

		return $this->_getTemplating()->render(self::_twigPhpIni, array(
																	   'vhost'  => $model,
																	   'global' => $globalConfig,
																  ));
	}

	/**
	 * Generate and save FCGI Starter Script
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @throws \Jboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 * @return void
	 */
	protected function _generateFcgiStarterForDomain(Domain $domain) {
		$filename = $domain->getPath() . '/php-fcgi/php-fcgi-starter.sh';

		if(!is_writable(dirname($filename))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Generating FCGI-Starter: ' . $filename);
		file_put_contents($filename, $this->_renderFcgiStarter($domain));

		// Change rights
		chmod($filename, 0755);

		// Change user & group
		chown($filename, $domain->getUser()->getName());
		chgrp($filename, $domain->getUser()->getGroupname());
	}

	/**
	 * Generate and save php.ini
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @throws \Jboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 */
	protected function _generatePhpIniForDomain(Domain $domain) {
		$filename = $domain->getPath() . '/conf/php.ini';

		if(!is_writable(dirname($filename))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Generating php.ini:' . $filename);
		file_put_contents($filename, $this->_renderPhpIni($this->_getVhostModelForDomain($domain)));

		// Change rights
		chmod($filename, 0440);

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
	 * @throws \Jboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 * @return void
	 */
	protected function _saveVhostConfig($filename, $config) {
		$target = $this
			->_getSystemConfigService()
			->getParameter('systemconfig.option.apache.config.directory') . '/' . $filename;

		if(!is_writable(dirname($target))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Creating new config: ' . $target);
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
		$this->_generatePhpIniForDomain($domain);
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

	/**
	 * Extracts servername from LampCP signature in config files
	 * TODO Validation could be better ;-)
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	protected function _getServernameFromConfigSignature($file) {
		$startSnippet = '[[[LAMPCP::';
		$endSnippet   = '::LAMPCP]]]';
		$posStart     = strpos($file, $startSnippet) + strlen($startSnippet);
		$posEnd       = strpos($file, $endSnippet);
		$length       = $posEnd - $posStart;
		$domain       = substr($file, $posStart, $length);

		if(strpos($domain, '.') !== false) {
			return $domain;
		}

		return '';
	}

	/**
	 * Look for obsolete config files
	 */
	public function cleanVhostDirectory() {
		$domainRepository = $this
			->_getDoctrine()
			->getRepository('JboehmLampcpCoreBundle:Domain');

		$dir = $this
			->_getSystemConfigService()
			->getParameter('systemconfig.option.apache.config.directory');

		$files = glob($dir . '/*' . self::_configFileSuffix);

		foreach($files as $file) {
			$content    = file_get_contents($file);
			$servername = $this->_getServernameFromConfigSignature($content);
			$skip       = false;

			if(!empty($servername)) {
				$domain = $domainRepository->findOneBy(array('domain' => $servername));

				if($domain) {
					continue;
				}

				foreach($this->_getAllSubdomains() as $subdomain) {
					if($subdomain->getFullDomain() === $servername) {
						$skip = true;
					}
				}

				if($skip) {
					continue;
				} else {
					$this->_getLogger()->info('(VhostBuilderService) Deleting obsolete config: ' . $file);
					unlink($file);
				}
			}
		}
	}
}
