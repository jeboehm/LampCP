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
use Jeboehm\Lampcp\ApacheConfigBundle\Service\CertificateBuilderService as ParentCertificateBuilderService;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;

class CertificateBuilderService extends ParentCertificateBuilderService implements BuilderServiceInterface {
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
				$this->_getLogger()->info('(CertificateBuilderService) Generating Cert.: ' . $fullfilename);

				if($ext === self::_EXTENSION_PRIVATEKEY) {
					$content = $this->_getCryptService()->decrypt($cert->$mGet());
				} else {
					$content = $cert->$mGet();
				}

				/**
				 * Write CertificateKeyFile into CertificateFile
				 * Write CertificateChainFile into CACertificateFile
				 */
				if($ext === self::_EXTENSION_PRIVATEKEY) {
					$fullfilename = $filename . self::_EXTENSION_CERTIFICATE;

					if($fs->exists($fullfilename)) {
						$crt = file_get_contents($fullfilename);
						$crt .= PHP_EOL;
					} else {
						$crt = '';
					}

					file_put_contents($fullfilename, $crt . $content);
				} elseif($ext === self::_EXTENSION_CACHAIN) {
					$fullfilename = $filename . self::_EXTENSION_CACERTIFICATE;

					if($fs->exists($fullfilename)) {
						$crt = file_get_contents($fullfilename);
						$crt .= PHP_EOL;
					} else {
						$crt = '';
					}

					file_put_contents($fullfilename, $crt . $content);
				} else {
					file_put_contents($fullfilename, $content);
					$cert->$mSetPath($fullfilename);
				}
			} else {
				if($fs->exists($fullfilename)) {
					$this->_getLogger()->info('(CertificateBuilderService) Deleting Cert.: ' . $fullfilename);

					$fs->remove($fullfilename);
				}

				$cert->$mSetPath('');
			}
		}
	}
}
