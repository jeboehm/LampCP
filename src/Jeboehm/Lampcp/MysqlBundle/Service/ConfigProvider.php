<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\Service;

use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

class ConfigProvider extends AbstractConfigProvider {
	public function init() {
		$group = $this->_createGroup('mysql');
		$this
			->_createEntity('rootuser', self::TYPE_STRING, $group, 'root')
			->_createEntity('rootpassword', self::TYPE_STRING, $group, '')
			->_createEntity('dbprefix', self::TYPE_STRING, $group, 'lampcpsql');
	}
}
