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
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;

class CertificateBuilderService extends AbstractBuilderService implements BuilderServiceInterface {
	const _EXTENSION_CERTIFICATE   = '.crt';
	const _EXTENSION_PRIVATEKEY    = '.key';
	const _EXTENSION_CACERTIFICATE = '.cacrt';
	const _EXTENSION_CACHAIN       = '.chain';

	protected $_extensions = array(
		self::_EXTENSION_CERTIFICATE,
		self::_EXTENSION_PRIVATEKEY,
		self::_EXTENSION_CACERTIFICATE,
		self::_EXTENSION_CACHAIN
	);

	/**
	 * Get certificate repository
	 *
	 * @return \Doctrine\ORM\EntityRepository
	 */
	protected function _getRepository() {
		return $this->_getDoctrine()->getRepository('JeboehmLampcpCoreBundle:Certificate');
	}

	/**
	 * Get certificate storage directory
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _getStorageDir() {
		$fs  = new Filesystem();
		$dir = $this->_getConfigService()->getParameter('apache.pathcertificate');

		if(empty($dir)) {
			$msg = '(CertificateBuilderService) Certificate Path config variable is empty!';
			$this->_getLogger()->err($msg);
			throw new \Exception($msg);
		}

		if(!$fs->exists($dir)) {
			$fs->mkdir($dir, 0750);
		}

		return $dir;
	}

	/**
	 * Save certificate
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Certificate $cert
	 */
	protected function _saveCertificate(Certificate $cert) {
		$target   = $this->_getStorageDir();
		$fs       = new Filesystem();
		$filename = $target . '/' . $cert->getId();

		foreach($this->_extensions as $ext) {
			$fullfilename = $filename . $ext;

			switch($ext) {
				case self::_EXTENSION_CERTIFICATE:
					$method = 'CertificateFile';
					break;

				case self::_EXTENSION_PRIVATEKEY:
					$method = 'CertificateKeyFile';
					break;

				case self::_EXTENSION_CACERTIFICATE:
					$method = 'CACertificateFile';
					break;

				case self::_EXTENSION_CACHAIN:
					$method = 'CertificateChainFile';
					break;
			}

			$mGet     = 'get' . $method;
			$mSetPath = 'set' . $method . 'Path';

			if($cert->$mGet()) {
				file_put_contents($fullfilename, $cert->$mGet());
				$fs->chmod($fullfilename, 0644);
				$cert->$mSetPath($fullfilename);
			} else {
				if($fs->exists($fullfilename)) {
					$fs->remove($fullfilename);
				}

				$cert->$mSetPath('');
			}
		}
	}

	/**
	 * Remove certificate from storage dir
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Certificate $cert
	 */
	protected function _deleteCertificate(Certificate $cert) {
		$fs       = new Filesystem();
		$filename = $this->_getStorageDir() . '/' . $cert->getId();

		foreach($this->_extensions as $ext) {
			$fullfilename = $filename . $ext;

			switch($ext) {
				case self::_EXTENSION_CERTIFICATE:
					$method = 'CertificateFile';
					break;

				case self::_EXTENSION_PRIVATEKEY:
					$method = 'CertificateKeyFile';
					break;

				case self::_EXTENSION_CACERTIFICATE:
					$method = 'CACertificateFile';
					break;

				case self::_EXTENSION_CACHAIN:
					$method = 'CertificateChainFile';
					break;
			}

			$mSetPath = 'set' . $method . 'Path';

			if($fs->exists($fullfilename)) {
				$fs->remove($fullfilename);
				$cert->$mSetPath('');
			}
		}
	}

	/**
	 * Clean up certificate directory
	 */
	protected function _removeUnusedCertificates() {
		$dir   = $this->_getStorageDir();
		$fs    = new Filesystem();
		$files = glob(sprintf('%s/*{%s}',
				$dir,
				join(',', $this->_extensions)
			), GLOB_BRACE
		);

		foreach($files as $path) {
			$filename                 = basename($path);
			$filenameWithoutExtension = substr($filename, 0, strpos($filename, '.'));

			if(!is_numeric($filenameWithoutExtension)) {
				continue;
			}

			/** @var $certificate Certificate */
			$certificate = $this->_getRepository()->findBy(array(
																'id' => intval($filenameWithoutExtension),
														   ));

			if($certificate) {
				continue;
			} else {
				$this->_getLogger()->info('(CertificateBuilderService) Removing unused certfile: ' . $filename);
				$fs->remove($path);
			}
		}
	}

	/**
	 * Build certificates
	 */
	public function buildAll() {
		foreach($this->_getRepository()->findAll() as $certificate) {
			/** @var $certificate Certificate */
			if(count($certificate->getDomain()) === 0
				&& count($certificate->getSubdomain()) === 0
			) {
				$this->_deleteCertificate($certificate);
			} else {
				$this->_saveCertificate($certificate);
			}
		}

		$this->_getDoctrine()->flush();
		$this->_removeUnusedCertificates();
	}
}
