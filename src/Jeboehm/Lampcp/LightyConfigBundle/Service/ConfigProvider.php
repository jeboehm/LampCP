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
use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;

class ConfigProvider extends AbstractConfigProvider {
	public function init() {
		$group = $this->_createGroup('lighttpd');
		$this
			->_createEntity('enabled', ConfigTypes::TYPE_BOOL, $group, true)
			->_createEntity('pathlighttpdconf', ConfigTypes::TYPE_STRING, $group, '/etc/lighttpd/conf-enabled')
			->_createEntity('cmdlighttpdrestart', ConfigTypes::TYPE_STRING, $group, '/etc/init.d/lighttpd restart');
	}
}
