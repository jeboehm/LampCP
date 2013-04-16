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
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\ApacheConfigBundle\Service\DirectoryBuilderService as ParentDirectoryBuilderService;

/**
 * Class DirectoryBuilderService
 *
 * Creates default folders per vhost in the filesystem.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DirectoryBuilderService extends ParentDirectoryBuilderService implements BuilderServiceInterface {
    const _http_user = 'www-data';

    /**
     * Create default directories.
     * Lighttpd writes logs as www-data, so we have to change the owner of the logs dir.
     *
     * @param \Jeboehm\Lampcp\CoreBundle\Entity\Domain $domain
     */
    protected function createDirectoriesForDomain(Domain $domain) {
        parent::createDirectoriesForDomain($domain);
        $fs = new Filesystem();

        if ($fs->exists($domain->getPath() . '/logs')) {
            $fs->chown($domain->getPath() . '/logs', self::_http_user, true);
        }
    }
}
