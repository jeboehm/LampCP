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

use Jeboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService as ParentDirectoryBuilderService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DirectoryBuilderService
 *
 * Creates default folders per vhost in the filesystem.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DirectoryBuilderService extends ParentDirectoryBuilderService
{
    const _http_user = 'www-data';

    /**
     * Lighttpd writes logs as www-data, so we have to change the owner of the logs directory.
     *
     * @param Domain $domain
     *
     * @return bool
     */
    protected function _changeDirectoryOwner(Domain $domain)
    {
        $result = parent::_changeDirectoryOwner($domain);

        $fs = new Filesystem();

        if ($fs->exists($domain->getPath() . '/logs')) {
            try {
                $fs->chown($domain->getPath() . '/logs', self::_http_user, true);
            } catch (IOException $e) {
                return false;
            }
        }

        return $result;
    }
}
