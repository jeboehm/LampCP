<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ApacheConfigBundle\Service;

use Jboehm\Lampcp\CoreBundle\Entity\Domain;
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jboehm\Lampcp\ApacheConfigBundle\Model\Vhost;
use Jboehm\Lampcp\ApacheConfigBundle\Exception\couldNotWriteFileException;

class DirectoryBuilderService extends AbstractBuilderService {
	const _root = 'root';
	const _dirs = 'conf,htdocs,logs,php-fcgi,tmp';

	/**
	 * Get default directorys
	 *
	 * @return array
	 */
	protected function _getDefaultDirs() {
		return explode(',', self::_dirs);
	}

	/**
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Domain $domain
	 */
	public function createDirectorysForDomain(Domain $domain) {
		if(!is_dir($domain->getPath())) {
			mkdir($domain->getPath());
		}

		// Rechte Domain-Root
		chmod($domain->getPath(), 0750);
		chown($domain->getPath(), self::_root);
		chgrp($domain->getPath(), $domain->getUser()->getGroupname());

		foreach($this->_getDefaultDirs() as $dir) {
			$path = $domain->getPath() . '/' . $dir;

			// Create directory
			if(!is_dir($path)) {
				mkdir($path);
			}

			// Change owner and group
			chown($path, $domain->getUser()->getName());
			chgrp($path, $domain->getUser()->getGroupname());

			// Change rights
			chmod($path, 0750);
		}
	}

	/**
	 * Create directory structure for all domains
	 */
	public function createDirectorysForAllDomains() {
		foreach($this->_getAllDomains() as $domain) {
			$this->createDirectorysForDomain($domain);
		}
	}
}
