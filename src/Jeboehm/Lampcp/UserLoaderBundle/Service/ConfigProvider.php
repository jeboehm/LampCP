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

use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;
use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

/**
 * Class ConfigProvider
 *
 * Provides default configuration fields for configuration service.
 *
 * @package Jeboehm\Lampcp\UserLoaderBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigProvider extends AbstractConfigProvider {
    /**
     * Add configuration entries
     */
    public function init() {
        $group = $this->_createGroup('unix');
        $this
            ->_createEntity('passwdfile', ConfigTypes::TYPE_STRING, $group, '/etc/passwd')
            ->_createEntity('groupfile', ConfigTypes::TYPE_STRING, $group, '/etc/group');
    }
}
