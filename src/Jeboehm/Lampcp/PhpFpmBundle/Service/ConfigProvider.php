<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\PhpFpmBundle\Service;

use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;
use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

/**
 * Class ConfigProvider
 *
 * Provides default configuration
 *
 * @package Jeboehm\Lampcp\PhpFpmBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigProvider extends AbstractConfigProvider {
    /**
     * Create configuration fields
     */
    public function init() {
        $group = $this->_createGroup('phpfpm');
        $this
            ->_createEntity('enabled', ConfigTypes::TYPE_BOOL, $group, true)
            ->_createEntity('pooldir', ConfigTypes::TYPE_STRING, $group, '/etc/php5/fpm/pool.d')
            ->_createEntity('socketdir', ConfigTypes::TYPE_STRING, $group, sys_get_temp_dir())
            ->_createEntity('cmd.reload', ConfigTypes::TYPE_STRING, $group, '/etc/init.d/php5-fpm reload');
    }
}
