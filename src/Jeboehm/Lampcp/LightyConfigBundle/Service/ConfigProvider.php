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

/**
 * Class ConfigProvider
 *
 * Provides the default configuration.
 *
 * @package Jeboehm\Lampcp\LightyConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigProvider extends AbstractConfigProvider {
    /**
     * Create configuration fields
     */
    public function init() {
        $group = $this->_createGroup('lighttpd');
        $this
            ->_createEntity('enabled', ConfigTypes::TYPE_BOOL, $group, true)
            ->_createEntity('pathlighttpdconf', ConfigTypes::TYPE_STRING, $group, '/etc/lighttpd/conf-enabled')
            ->_createEntity('cmdlighttpdrestart', ConfigTypes::TYPE_STRING, $group, '/etc/init.d/lighttpd restart');
    }
}
