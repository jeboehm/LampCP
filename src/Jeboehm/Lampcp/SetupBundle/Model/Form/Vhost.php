<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Model\Form;

use Jeboehm\Lampcp\SetupBundle\Model\Annotation\Form;

/**
 * Class Vhost
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Form
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Vhost extends AbstractForm
{
    /**
     * @Form(message="Address to access LampCP", type="ask")
     */
    public $address;
    /**
     * @Form(message="IP address of the new vhost", type="ask")
     */
    public $ipaddress;
    /**
     * @Form(message="LampCP web scripts should run under this system user", type="ask")
     */
    public $user;
}
