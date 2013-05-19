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

use Jeboehm\Lampcp\ApacheConfigBundle\Exception\EmptyCertificatePathException;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CertificateBuilderService
 *
 * Stores SSL certificates in the filesystem.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class CertificateBuilderService
{
    /** Certificate */
    const _EXTENSION_CERTIFICATE = '.crt';

    /** Private key */
    const _EXTENSION_PRIVATEKEY = '.key';

    /** CA Certificate */
    const _EXTENSION_CACERTIFICATE = '.cacrt';

    /** CA Chain */
    const _EXTENSION_CACHAIN = '.chain';

    /** @var string */
    private $_storageDir;

    /** @var CryptService */
    private $_cryptService;

    /** @var Certificate[] */
    private $_certificates;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_certificates = array();
    }

    /**
     * Set certificates.
     *
     * @param array $certificates
     *
     * @return $this
     */
    public function setCertificates(array $certificates)
    {
        $this->_certificates = $certificates;

        return $this;
    }

    /**
     * Get certificates.
     *
     * @return Certificate[]
     */
    public function getCertificates()
    {
        return $this->_certificates;
    }

    /**
     * Get certificate by id.
     *
     * @param int $id
     *
     * @return Certificate|null
     */
    public function getCertificateById($id)
    {
        foreach ($this->getCertificates() as $certificate) {
            if ($certificate->getId() === $id) {
                return $certificate;
            }
        }

        return null;
    }

    /**
     * Set CryptService.
     *
     * @param CryptService $cryptService
     *
     * @return CertificateBuilderService
     */
    public function setCryptService(CryptService $cryptService)
    {
        $this->_cryptService = $cryptService;

        return $this;
    }

    /**
     * Get CryptService.
     *
     * @return CryptService
     */
    public function getCryptService()
    {
        return $this->_cryptService;
    }

    /**
     * Get storage directory.
     *
     * @throws EmptyCertificatePathException
     * @return string
     */
    public function getStorageDir()
    {
        if (empty($this->_storageDir)) {
            throw new EmptyCertificatePathException();
        }

        return $this->_storageDir;
    }

    /**
     * Set storage directory.
     *
     * @param string $dir
     *
     * @return $this
     */
    public function setStorageDir($dir)
    {
        $fs = new Filesystem();

        if (!$fs->exists($dir)) {
            $fs->mkdir($dir, 0750);
        }

        $this->_storageDir = $dir;

        return $this;
    }

    /**
     * Get certificate file extensions.
     *
     * @return array
     */
    protected function _getExtensions()
    {
        return array(
            self::_EXTENSION_CERTIFICATE,
            self::_EXTENSION_PRIVATEKEY,
            self::_EXTENSION_CACERTIFICATE,
            self::_EXTENSION_CACHAIN
        );
    }

    /**
     * Save certificate.
     *
     * @param Certificate $certificate
     */
    public function saveCertificate(Certificate $certificate)
    {
        $fs       = new Filesystem();
        $filename = $this->getStorageDir() . '/' . $certificate->getId();
        $method   = '';

        foreach ($this->_getExtensions() as $ext) {
            $fullfilename = $filename . $ext;

            switch ($ext) {
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

            /** @var string $mGet Method for getting certificate variable. */
            $mGet = 'get' . $method;

            /** @var string $mSetPath Method for setting certificate variable. */
            $mSetPath = 'set' . $method . 'Path';

            $content = $certificate->{$mGet}();

            if ($content) {
                // Data has to be decrypted.
                if ($ext === self::_EXTENSION_PRIVATEKEY && $this->getCryptService()) {
                    $content = $this
                        ->getCryptService()
                        ->decrypt($content);
                }

                file_put_contents($fullfilename, $content);

                if ($ext === self::_EXTENSION_PRIVATEKEY) {
                    // Private keys should be more secured.
                    $fs->chmod($fullfilename, 0600);
                } else {
                    $fs->chmod($fullfilename, 0644);
                }

                $certificate->{$mSetPath}($fullfilename);
            } else {
                // Remove empty certificate files.
                if ($fs->exists($fullfilename)) {
                    $fs->remove($fullfilename);
                }

                // Set their path to empty.
                $certificate->{$mSetPath}('');
            }
        }
    }

    /**
     * Remove certificate from storage dir.
     *
     * @param Certificate $certificate
     */
    public function deleteCertificate(Certificate $certificate)
    {
        $fs       = new Filesystem();
        $filename = $this->getStorageDir() . '/' . $certificate->getId();
        $method   = '';

        foreach ($this->_getExtensions() as $ext) {
            $fullfilename = $filename . $ext;

            switch ($ext) {
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

            if ($fs->exists($fullfilename)) {
                $fs->remove($fullfilename);
                $certificate->{$mSetPath}('');
            }
        }
    }

    /**
     * Remove unused certificate files.
     */
    public function removeUnusedCertificateFiles()
    {
        $dir   = $this->getStorageDir();
        $fs    = new Filesystem();
        $files = glob(sprintf('%s/*{%s}', $dir, join(',', $this->_getExtensions())), GLOB_BRACE);

        foreach ($files as $path) {
            $filename                 = basename($path);
            $filenameWithoutExtension = substr($filename, 0, strpos($filename, '.'));

            if (!is_numeric($filenameWithoutExtension)) {
                continue;
            }

            $id          = intval($filenameWithoutExtension);
            $certificate = $this->getCertificateById($id);

            // Remove file from filesystem, when its certificate is not found.
            if (!$certificate) {
                $fs->remove($path);
            }
        }
    }

    /**
     * Build all certificates.
     * Removes the unused, saves the used ones.
     */
    public function buildCertificates()
    {
        foreach ($this->getCertificates() as $certificate) {
            $uses = count($certificate->getDomain()) + count($certificate->getSubdomain());

            if ($uses < 1) {
                $this->deleteCertificate($certificate);
            } else {
                $this->saveCertificate($certificate);
            }
        }
    }
}
