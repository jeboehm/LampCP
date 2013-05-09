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

use Jeboehm\Lampcp\ApacheConfigBundle\Service\CertificateBuilderService as ParentCertificateBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Certificate;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CertificateBuilderService
 *
 * Stores SSL certificates in the filesystem.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class CertificateBuilderService extends ParentCertificateBuilderService
{
    /**
     * Save certificates.
     * For Lighttpd, merge some files into others.
     *
     * @param Certificate $certificate
     */
    public function saveCertificate(Certificate $certificate)
    {
        $fs = new Filesystem();
        parent::saveCertificate($certificate);

        /**
         * Merge Key into Cert-File,
         * and merge Chain into CA-Cert file.
         */

        $keyfile      = $certificate->getCertificateKeyFile();
        $chainfile    = $certificate->getCertificateChainFile();
        $cafilepath   = $certificate->getCACertificateFilePath();
        $certfilepath = $certificate->getCertificateFilePath();

        if (!empty($keyfile) && $fs->exists($certfilepath)) {
            file_put_contents($certfilepath, PHP_EOL . $keyfile, FILE_APPEND);
        }

        if (!empty($chainfile) && $fs->exists($cafilepath)) {
            file_put_contents($cafilepath, PHP_EOL . $chainfile, FILE_APPEND);
        }
    }
}
