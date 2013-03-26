<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Service;

use Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider;
use Jeboehm\Lampcp\ConfigBundle\Model\ConfigTypes;

/**
 * Class ConfigProvider
 *
 * Provides the default configuration.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\Service
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigProvider extends AbstractConfigProvider {
    /**
     * Create configuration fields.
     */
    public function init() {
        $group = $this->_createGroup('core');
        $this->_createEntity('installdate', ConfigTypes::TYPE_HIDDEN, $group, time());
    }
}
