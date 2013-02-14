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

use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

class ConfigProvider extends AbstractConfigProvider {
	public function init() {
		$group = $this->_createGroup('lighttpd');
		$this
			->_createEntity('pathlighttpdconf', self::TYPE_STRING, $group, '/etc/lighttpd/conf-enabled')
			->_createEntity('cmdlighttpdrestart', self::TYPE_STRING, $group, '/etc/init.d/lighttpd restart');
	}
}
