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

	/**
	 * Create directory structure for all domains
	 */
	public function createAllDirectorys() {
		foreach($this->_getAllDomains() as $domain) {
			if(!is_dir($domain->getPath())) {
				mkdir($domain->getPath());
			}

			// Rechte Domain-Root
			chmod($domain->getPath(), 0750);
			chown($domain->getPath(), self::_root);
			chgrp($domain->getPath(), $domain->getUser()->getGroupname());

			$dirs = array('conf', 'htdocs', 'logs', 'php-fcgi', 'tmp');

			foreach($dirs as $dir) {
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
	}
}
