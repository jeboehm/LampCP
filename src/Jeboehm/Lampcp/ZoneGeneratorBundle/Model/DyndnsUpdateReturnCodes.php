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

class DyndnsUpdateReturnCodes {
    const SUCCESS             = 'good';
    const NO_CHANGE           = 'nochg';
    const CREDENTIALS         = 'badauth';
    const WRONG_DOMAIN_FORMAT = 'notfqdn';
    const DOMAIN_NOT_FOUND    = 'nohost';
    const TOO_MANY_HOSTS      = 'numhost';
    const ABUSE               = 'abuse';
    const MAINTENANCE         = '911';
}