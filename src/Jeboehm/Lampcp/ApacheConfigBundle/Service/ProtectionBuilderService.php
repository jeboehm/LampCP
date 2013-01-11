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
use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;
use Jeboehm\Lampcp\ApacheConfigBundle\Model\Protection as ProtectionConfigModel;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection as ProtectionEntity;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;

class ProtectionBuilderService extends AbstractBuilderService {
	const _twigAuthUserFile         = 'JeboehmLampcpApacheConfigBundle:Default:AuthUserFile.conf.twig';
	const _twigApacheProtectionConf = 'JeboehmLampcpApacheConfigBundle:Default:protections.conf.twig';

	/**
	 * Get protection model array
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Protection $protection
	 *
	 * @return \Jeboehm\Lampcp\ApacheConfigBundle\Model\Protection[]
	 */
	protected function _getProtectionModelArray(ProtectionEntity $protection) {
		$models = array();

		foreach($protection->getProtectionuser() as $prot) {
			$mod = new ProtectionConfigModel();
			$mod
				->setId($prot->getId())
				->setUsername($prot->getUsername())
				->setPassword($this->_getCryptService()->decrypt($prot->getPassword()));

			$models[] = $mod;
		}

		return $models;
	}

	/**
	 * Render AuthUserFile
	 *
	 * @param array $models
	 *
	 * @return string
	 */
	protected function _renderAuthUserFile(array $models) {
		return $this->_getTemplating()->render(self::_twigAuthUserFile, array(
																			 'users' => $models,
																		));
	}

	/**
	 * Render Apache's Protection config
	 *
	 * @param ProtectionEntity[] $protections
	 *
	 * @return string
	 */
	protected function _renderApacheProtectionConf(array $protections) {
		return $this->_getTemplating()->render(self::_twigApacheProtectionConf, array(
																					 'protections' => $protections,
																				));
	}

	/**
	 * Generate AuthUserFile
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Protection $protection
	 *
	 * @throws \Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException
	 */
	protected function _generateAuthUserFile(ProtectionEntity $protection) {
		$fs               = new Filesystem();
		$pathAuthUserFile = sprintf(
			'%s/conf/authuser_%s.passwd',
			$protection->getDomain()->getPath(),
			$protection->getId()
		);

		if(!is_writable(dirname($pathAuthUserFile))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(ProtectionBuilderService) Generating AuthUserFile:' . $pathAuthUserFile);
		file_put_contents($pathAuthUserFile, $this->_renderAuthUserFile($this->_getProtectionModelArray($protection)));

		// Change rights
		$fs->chmod($pathAuthUserFile, 0440);

		// Change user & group
		$fs->chown($pathAuthUserFile, $protection->getDomain()->getUser()->getName());
		$fs->chgrp($pathAuthUserFile, $protection->getDomain()->getUser()->getGroupname());
	}

	/**
	 * Generate Apache's Protection config
	 */
	protected function _generateApacheProtectionConfig() {
		/** @var $protections ProtectionEntity[] */
		$apacheConfigDir = $this->_getConfigService()->getParameter('apache.pathapache2conf');
		$filename        = '98_protections.conf';
		$configFilePath  = $apacheConfigDir . '/' . $filename;
		$protections     = $this->_getDoctrine()->getRepository('JeboehmLampcpCoreBundle:Protection')->findAll();
		$config          = $this->_renderApacheProtectionConf($protections);

		$this->_getLogger()->info('(ProtectionBuilderService) Generating Protection Config:' . $configFilePath);
		file_put_contents($configFilePath, $config);
	}

	/**
	 * Build domain's protections configuration
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 *
	 * @return void
	 */
	protected function _buildDomain(Domain $domain) {
		foreach($domain->getProtection() as $protection) {
			$this->_generateAuthUserFile($protection);
		}
	}

	/**
	 * Build all configurations
	 */
	public function buildAll() {
		foreach($this->_getAllDomains() as $domain) {
			$this->_buildDomain($domain);
		}

		$this->_generateApacheProtectionConfig();
	}

	/**
	 * Look for obsolete AuthUserFile files
	 */
	public function cleanConfDirectory() {
		/** @var $domains Domain[] */
		$fs      = new Filesystem();
		$domains = $this
			->_getDoctrine()
			->getRepository('JeboehmLampcpCoreBundle:Domain')->findAll();

		foreach($domains as $domain) {
			$dir   = $domain->getPath() . '/conf';
			$files = glob($dir . '/authuser_*.passwd');

			foreach($files as $filepath) {
				$idStart = strpos($filepath, 'authuser_') + strlen('authuser_');
				$idEnd   = strpos($filepath, '.passwd');
				$id      = intval(substr($filepath, $idStart, ($idEnd - $idStart)));

				$protection = $this
					->_getDoctrine()
					->getRepository('JeboehmLampcpCoreBundle:Protection')
					->findOneBy(array(
									 'id'     => $id,
									 'domain' => $domain->getId(),
								));

				if(!$protection) {
					$this->_getLogger()->info('(ProtectionBuilderService) Deleting obsolete AuthUserFile: ' . $filepath);
					$fs->remove($filepath);
				}
			}
		}
	}
}
