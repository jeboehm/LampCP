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
use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;

class ConfigProvider extends AbstractConfigProvider {
	public function init() {
		$group = $this->_createGroup('mysql');
		$this
			->_createEntity('enabled', ConfigTypes::TYPE_BOOL, $group, true)
			->_createEntity('rootuser', ConfigTypes::TYPE_STRING, $group, 'root')
			->_createEntity('rootpassword', ConfigTypes::TYPE_PASSWORD, $group, '')
			->_createEntity('dbprefix', ConfigTypes::TYPE_STRING, $group, 'lampcpsql');
	}
}
