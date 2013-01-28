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

use Symfony\Component\Filesystem\Filesystem;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\IpAddress;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\VhostBuilderService as ParentVhostBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;
use Jeboehm\Lampcp\LightyConfigBundle\Model\Vhost;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;

class VhostBuilderService extends ParentVhostBuilderService implements BuilderServiceInterface {
	const _twigVhost       = 'JeboehmLampcpLightyConfigBundle:Lighttpd:vhost.conf.twig';
	const _twigFcgiStarter = 'JeboehmLampcpLightyConfigBundle:PHP:php-fcgi-starter.sh.twig';

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
			->getParameter('lighttpd.pathlighttpdconf') . '/' . self::_domainFileName;

		$content = str_replace('  ', '', $content);
		$content = str_replace(PHP_EOL . PHP_EOL, PHP_EOL, $content);

		if(!is_writable(dirname($target))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(VhostBuilderService) Creating new config: ' . $target);
		file_put_contents($target, $content);
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

		if(!$fs->exists($filename)) {
			$content = $this->_renderTemplate(self::_twigFcgiStarter, array(
																		   'domain' => $domain,
																	  ));

			$this->_getLogger()->info('(VhostBuilderService) Generating FCGI-Starter: ' . $filename);
			file_put_contents($filename, $content);
		}

		// Change rights
		$fs->chmod($filename, 0755);

		// Change user & group
		$fs->chown($filename, $domain->getUser()->getName());
		$fs->chgrp($filename, $domain->getUser()->getGroupname());
	}

	/**
	 * Build all configurations
	 */
	public function buildAll() {
		/** @var $models Vhost[] */
		$models = array();

		foreach($this->_getAllDomains() as $domain) {
			if($domain->getIpaddress()->count() > 0) {
				foreach($domain->getIpaddress() as $ipaddress) {
					/** @var $ipaddress IpAddress */
					$vhost = new Vhost();
					$vhost
						->setDomain($domain)
						->setIpaddress($ipaddress);

					if($vhost->getSSLEnabled() || !$ipaddress->getHasSsl()) {
						$models[] = $vhost;
					}
				}
			} else {
				$vhost = new Vhost();
				$vhost->setDomain($domain);
				$models[] = $vhost;
			}

			$this->_generatePhpIniForDomain($domain);
			$this->_generateFcgiStarterForDomain($domain);
		}

		foreach($this->_getAllSubdomains() as $subdomain) {
			if($subdomain->getDomain()->getIpaddress()->count() > 0) {
				foreach($subdomain->getDomain()->getIpaddress() as $ipaddress) {
					/** @var $ipaddress IpAddress */
					$vhost = new Vhost();
					$vhost
						->setDomain($subdomain->getDomain())
						->setSubdomain($subdomain)
						->setIpaddress($ipaddress);

					if($vhost->getSSLEnabled() || !$ipaddress->getHasSsl()) {
						$models[] = $vhost;
					}
				}
			} else {
				$vhost = new Vhost();
				$vhost
					->setDomain($subdomain->getDomain())
					->setSubdomain($subdomain);
				$models[] = $vhost;
			}
		}

		$models  = $this->_orderVhosts($models);
		$content = $this->_renderTemplate(self::_twigVhost, array(
																 'defaultcert' => $this->_getSingleCertificateWithDomainsAssigned(),
																 'vhosts'      => $models,
																 'ips'         => $this->_getAllIpAddresses(),
															));

		$this->_saveVhostConfig($content);
	}

	/**
	 * Get single certificate with domain / subdomain set
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Certificate|null
	 */
	protected function _getSingleCertificateWithDomainsAssigned() {
		/** @var $certs Certificate[] */
		$certs = $this
			->_getDoctrine()
			->getRepository('JeboehmLampcpCoreBundle:Certificate')
			->findAll();

		foreach($certs as $certificate) {
			/** @var $certificate Certificate */
			if($certificate->getDomain()->count() > 0 || $certificate->getSubdomain()->count() > 0) {
				return $certificate;
			}
		}

		return null;
	}
}
