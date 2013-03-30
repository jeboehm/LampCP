<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Form\Model;

/**
 * Class AdminRoles
 *
 * Holds the valid user roles with their translation
 * strings.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Form\Model
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class AdminRoles {
    static public $roles = array(
        'ROLE_SUPER_ADMIN' => 'security.roles.super_admin',
        'ROLE_DYNDNS'      => 'security.roles.dyndns',
    );
}