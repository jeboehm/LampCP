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

use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;
use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

/**
 * Class ConfigProvider
 *
 * Provides default configuration
 *
 * @package Jeboehm\Lampcp\MysqlBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigProvider extends AbstractConfigProvider {
    /**
     * Create configuration fields
     */
    public function init() {
        $group = $this->_createGroup('mysql');
        $this
            ->_createEntity('enabled', ConfigTypes::TYPE_BOOL, $group, true)
            ->_createEntity('rootuser', ConfigTypes::TYPE_STRING, $group, 'root')
            ->_createEntity('rootpassword', ConfigTypes::TYPE_PASSWORD, $group, '')
            ->_createEntity('host', ConfigTypes::TYPE_STRING, $group, '127.0.0.1')
            ->_createEntity('port', ConfigTypes::TYPE_INTEGER, $group, 3306)
            ->_createEntity('dbprefix', ConfigTypes::TYPE_STRING, $group, 'lampcpsql');
    }
}
