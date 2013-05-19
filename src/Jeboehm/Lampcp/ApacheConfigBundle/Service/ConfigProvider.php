<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Service;

use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;
use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;

/**
 * Class ConfigProvider
 *
 * Provides the default configuration.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigProvider extends AbstractConfigProvider
{
    /**
     * Create configuration fields.
     */
    public function init()
    {
        $group = $this->_createGroup('apache');
        $this
            ->_createEntity('enabled', ConfigTypes::TYPE_BOOL, $group, true)
            ->_createEntity('pathwww', ConfigTypes::TYPE_STRING, $group, '/var/www')
            ->_createEntity('pathapache2conf', ConfigTypes::TYPE_STRING, $group, '/etc/apache2/sites-enabled')
            ->_createEntity('cmdapache2restart', ConfigTypes::TYPE_STRING, $group, 'service apache2 restart')
            ->_createEntity('pathcertificate', ConfigTypes::TYPE_STRING, $group, '/etc/ssl/lampcp');
    }
}
