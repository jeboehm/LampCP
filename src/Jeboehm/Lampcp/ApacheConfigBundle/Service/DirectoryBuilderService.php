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

use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DirectoryBuilderService
 *
 * Creates default directories in the filesystem.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DirectoryBuilderService
{
    /** Use this as the root user. */
    const _root = 'root';

    /** These are the default directories. */
    const _dirs = 'conf,htdocs,logs,tmp';

    /**
     * Get default directories.
     *
     * @param string $basepath
     *
     * @return array
     */
    protected function _getDefaultDirs($basepath)
    {
        $directoryname = explode(',', self::_dirs);
        $fullpath      = array();

        foreach ($directoryname as $dn) {
            $fullpath[] = $basepath . '/' . $dn;
        }

        return $fullpath;
    }

    /**
     * Create default directories for domain.
     *
     * @param Domain $domain
     */
    public function createDirectories(Domain $domain)
    {
        $fs   = new Filesystem();
        $dirs = array_merge(array($domain->getPath()), $this->_getDefaultDirs($domain->getPath()));

        $fs->mkdir($dirs, 0750);
        $fs->chmod($dirs, 0750);

        $this->_changeDirectoryOwner($domain);
    }

    /**
     * Change directory owners.
     *
     * @param Domain $domain
     *
     * @return bool
     */
    protected function _changeDirectoryOwner(Domain $domain)
    {
        $fs = new Filesystem();

        try {
            $fs->chown($domain->getPath(), self::_root); // Domain Root
            $fs->chgrp(
                $domain->getPath(),
                $domain
                    ->getUser()
                    ->getGroupname(),
                true
            ); // Domain Root + Child

            // Child directories
            $fs->chown(
                $this->_getDefaultDirs($domain->getPath()),
                $domain
                    ->getUser()
                    ->getName(),
                true
            );
        } catch (IOException $e) {
            return false;
        }

        return true;
    }
}
