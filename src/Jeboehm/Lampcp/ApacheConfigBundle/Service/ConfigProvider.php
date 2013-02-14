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

class ConfigProvider extends AbstractConfigProvider {
	public function init() {
		$group = $this->_createGroup('apache');
		$this
			->_createEntity('pathwww', self::TYPE_STRING, $group, '/var/www')
			->_createEntity('pathapache2conf', self::TYPE_STRING, $group, '/etc/apache2/sites-enabled')
			->_createEntity('pathphpini', self::TYPE_STRING, $group, '/etc/php5/cgi/php.ini')
			->_createEntity('cmdapache2restart', self::TYPE_STRING, $group, 'service apache2 restart')
			->_createEntity('pathcertificate', self::TYPE_STRING, $group, '/etc/ssl/lampcp');
	}
}
