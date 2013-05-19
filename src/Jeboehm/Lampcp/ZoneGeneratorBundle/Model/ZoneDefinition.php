<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Model;

/**
 * Class ZoneDefinition
 *
 * Create zone definitions.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ZoneDefinition {
    const _DEFINITION = <<< EOT
zone "%name%" IN {
    type master;
    file "%path%";
};
EOT;

    /**
     * Get zone file definition.
     *
     * @param string $name
     * @param string $dbfile
     *
     * @return string
     */
    static public function create($name, $dbfile) {
        $find    = array(
            '%name%',
            '%path%',
        );
        $replace = array(
            $name,
            $dbfile,
        );

        $text = str_replace($find, $replace, self::_DEFINITION);

        return $text;
    }
}