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
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;

class VhostBuilderService extends AbstractBuilderService implements BuilderInterface {
	const _twigVhost         = 'JeboehmLampcpApacheConfigBundle:Default:vhost.conf.twig';
	const _twigFcgiStarter   = 'JeboehmLampcpApacheConfigBundle:Default:php-fcgi-starter.sh.twig';
	const _twigPhpIni        = 'JeboehmLampcpApacheConfigBundle:Default:php.ini.twig';
	const _configFileSuffix  = '.conf';
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
	 */
	protected function _renderVhostConfig(Vhost $model) {
		return $this->_getTemplating()->render(self::_twigVhost, array('vhost' => $model));
	}

	/**
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @return string
	 */
	protected function _renderFcgiStarter(Domain $domain) {
		return $this->_getTemplating()->render(self::_twigFcgiStarter, array('domain' => $domain));
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

		return $this->_getTemplating()->render(self::_twigPhpIni, array(
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

		if(!is_writable(dirname($filename))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Generating FCGI-Starter: ' . $filename);
		file_put_contents($filename, $this->_renderFcgiStarter($domain));

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
	 * @param string $filename
	 * @param string $config
	 *
	 * @throws \Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 * @return void
	 */
	protected function _saveVhostConfig($filename, $config) {
		$target = $this
			->_getConfigService()
			->getParameter('apache.pathapache2conf') . '/' . $filename;

		if(!is_writable(dirname($target))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Creating new config: ' . $target);
		file_put_contents($target, $config);
	}

	/**
	 * Build domain configuration
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 */
	protected function _buildDomain(Domain $domain) {
		$filename = $domain->getDomain() . self::_configFileSuffix;
		$config   = $this->_renderVhostConfig($this->_getVhostModelForDomain($domain));

		$this->_saveVhostConfig($filename, $config);
		$this->_generateFcgiStarterForDomain($domain);
		$this->_generatePhpIniForDomain($domain);
	}

	/**
	 * Build subdomain configuration
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Subdomain $subdomain
	 */
	protected function _buildSubdomain(Subdomain $subdomain) {
		$filename = $subdomain->getFullDomain() . self::_configFileSuffix;
		$config   = $this->_renderVhostConfig($this->_getVhostModelForSubdomain($subdomain));

		$this->_saveVhostConfig($filename, $config);
	}

	/**
	 * Build all configurations
	 */
	public function buildAll() {
		foreach($this->_getAllDomains() as $domain) {
			$this->_buildDomain($domain);
		}

		foreach($this->_getAllSubdomains() as $subdomain) {
			$this->_buildSubdomain($subdomain);
		}

		$this->_cleanVhostDirectory();
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
	protected function _cleanVhostDirectory() {
		$fs               = new Filesystem();
		$domainRepository = $this
			->_getDoctrine()
			->getRepository('JeboehmLampcpCoreBundle:Domain');
		$dir              = $this
			->_getConfigService()
			->getParameter('apache.pathapache2conf');
		$files            = glob($dir . '/*' . self::_configFileSuffix);

		foreach($files as $file) {
			$content    = file_get_contents($file);
			$servername = $this->_getServernameFromConfigSignature($content);
			$skip       = false;
			$domain     = null;

			if(!empty($servername)) {
				$domain = $domainRepository->findOneBy(array('domain' => $servername));

				if($domain) {
					continue;
				}

				foreach($this->_getAllSubdomains() as $subdomain) {
					if($subdomain->getFullDomain() === $servername) {
						$skip = true;
						continue;
					}
				}

				if($skip) {
					continue;
				} else {
					$this->_getLogger()->info('(VhostBuilderService) Deleting obsolete config: ' . $file);
					$fs->remove($file);
				}
			}
		}
	}
}
