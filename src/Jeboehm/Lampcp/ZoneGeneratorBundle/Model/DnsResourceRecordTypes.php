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
