<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Helper;

use Jeboehm\Lampcp\CoreBundle\Model\OpensslCertificate;

/**
 * Class Openssl
 *
 * Provides helper methods for reading ssl certificates.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Helper
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Openssl
{
    /**
     * Get certificate info.
     *
     * @param string $certificate
     *
     * @return OpensslCertificate
     * @throws \InvalidArgumentException
     */
    public function getInfo($certificate)
    {
        $result = openssl_x509_parse($certificate);

        if (!is_array($result)) {
            throw new \InvalidArgumentException('Could not parse certificate.');
        }

        return new OpensslCertificate($result);
    }
}
