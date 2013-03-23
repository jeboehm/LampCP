<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord;

class CNAME extends AbstractResourceRecord {
    /**
     * Get class name
     *
     * @return string
     */
    protected function _getClassName() {
        return __CLASS__;
    }
}
