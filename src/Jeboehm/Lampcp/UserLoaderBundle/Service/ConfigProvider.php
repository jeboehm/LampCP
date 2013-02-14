<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UserLoaderBundle\Service;

use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

class ConfigProvider extends AbstractConfigProvider {
	public function init() {
		$group = $this->_createGroup('unix');
		$this
			->_createEntity('passwdfile', self::TYPE_STRING, $group, '/etc/passwd')
			->_createEntity('groupfile', self::TYPE_STRING, $group, '/etc/group');
	}
}
