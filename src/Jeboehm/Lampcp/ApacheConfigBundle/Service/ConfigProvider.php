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

use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;
use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;

class ConfigProvider extends AbstractConfigProvider {
	public function init() {
		$group = $this->_createGroup('apache');
		$this
			->_createEntity('enabled', ConfigTypes::TYPE_BOOL, $group, true)
			->_createEntity('pathwww', ConfigTypes::TYPE_STRING, $group, '/var/www')
			->_createEntity('pathapache2conf', ConfigTypes::TYPE_STRING, $group, '/etc/apache2/sites-enabled')
			->_createEntity('pathphpini', ConfigTypes::TYPE_STRING, $group, '/etc/php5/cgi/php.ini')
			->_createEntity('cmdapache2restart', ConfigTypes::TYPE_STRING, $group, 'service apache2 restart')
			->_createEntity('pathcertificate', ConfigTypes::TYPE_STRING, $group, '/etc/ssl/lampcp');
	}
}
