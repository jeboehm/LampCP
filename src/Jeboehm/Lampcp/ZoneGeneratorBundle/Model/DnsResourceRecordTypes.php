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
 * Class DnsResourceRecordTypes
 *
 * Provides a list of the available resource record types.
 *
 * @package Jeboehm\Lampcp\ZoneGeneratorBundle\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DnsResourceRecordTypes {
    static $types = array(
        'A',
        'AAAA',
        'CNAME',
        'MX',
        'NS',
        'PTR',
        'SOA',
        'SPF',
        'TXT',
    );
}
