<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Utilities;

/**
 * Class FilesizeUtility
 *
 * Returns human readable sizes
 *
 * @package Jeboehm\Lampcp\CoreBundle\Utilities
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class FilesizeUtility {
    /**
     * Return human readable sizes
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.3.0
     * @link        http://aidanlister.com/2004/04/human-readable-file-sizes/
     *
     * @param       int    $size        size in kib
     * @param       string $max         maximum unit
     * @param       string $system      'si' for SI, 'bi' for binary prefixes
     * @param       string $retstring   return string format
     *
     * @return string
     */
    public static function size_readable($size, $max = null, $system = 'bi', $retstring = '%01.2f %s') {
        // Pick units
        $systems['si']['prefix'] = array('K', 'MB', 'GB', 'TB', 'PB');
        $systems['si']['size']   = 1000;
        $systems['bi']['prefix'] = array('KiB', 'MiB', 'GiB', 'TiB', 'PiB');
        $systems['bi']['size']   = 1024;
        $sys                     = isset($systems[$system]) ? $systems[$system] : $systems['si'];

        // Max unit to display
        $depth = count($sys['prefix']) - 1;
        if ($max && false !== $d = array_search($max, $sys['prefix'])) {
            $depth = $d;
        }

        // Loop
        $i = 0;
        while ($size >= $sys['size'] && $i < $depth) {
            $size /= $sys['size'];
            $i++;
        }

        return sprintf($retstring, $size, $sys['prefix'][$i]);
    }
}
