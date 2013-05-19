<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer;

use Jeboehm\Lampcp\CoreBundle\Entity\Dns;
use Jeboehm\Lampcp\ZoneGeneratorBundle\Model\ResourceRecord\AbstractResourceRecord;

/**
 * Class FqdnTransformer
 *
 * Transform an AbstractResourceRecord to a FQDN.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Model\Transformer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class FqdnTransformer {
    /**
     * Transform resource record to FQDN.
     *
     * @param Dns                    $dns
     * @param AbstractResourceRecord $rr
     *
     * @return string
     */
    static public function getFqdn(Dns $dns, AbstractResourceRecord $rr) {
        if (substr($rr->getName(), -1, 1) === '.') {
            $fqdn = substr($rr->getName(), 0, strlen($rr->getName()) - 1);
        } elseif ($rr->getName() == '@') {
            $fqdn = $dns->getOrigin();
        } else {
            $fqdn = $rr->getName() . '.' . $dns->getOrigin();
        }

        return $fqdn;
    }
}