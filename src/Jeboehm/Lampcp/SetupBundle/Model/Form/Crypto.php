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
 * Class Crypto
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Form
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Crypto extends AbstractForm
{
    /**
     * @Form(message="Framework secret key (random alphanum)", type="ask")
     */
    public $framework_secret;
    /**
     * @Form(message="Password to encrypt account passwords", type="ask")
     */
    public $crypt;
}
