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
 * Class Database
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Form
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Database extends AbstractForm
{
    /**
     * @Form(message="Hostname", type="ask")
     */
    public $hostname;
    /**
     * @Form(message="Username", type="ask")
     */
    public $username;
    /**
     * @Form(message="Password", type="askHiddenResponse")
     */
    public $password;
    /**
     * @Form(message="Database", type="ask")
     */
    public $database;
}
