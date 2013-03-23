<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Service;

use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;
use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

class ConfigProvider extends AbstractConfigProvider {
    public function init() {
        $group = $this->_createGroup('dns');

        $this
            ->_createEntity('default.ns', ConfigTypes::TYPE_STRING, $group, 'ns.example.com.')
            ->_createEntity('config.zonedir', ConfigTypes::TYPE_STRING, $group, '/etc/bind/lampcp')
            ->_createEntity('config.zonedef', ConfigTypes::TYPE_STRING, $group, '/etc/bind/named.conf.local')
            ->_createEntity('cmd.reload', ConfigTypes::TYPE_STRING, $group, '/etc/init.d/bind9 reload');
    }
}
