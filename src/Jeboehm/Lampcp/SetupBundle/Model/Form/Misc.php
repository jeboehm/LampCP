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
 * Class Misc
 *
 * @package Jeboehm\Lampcp\SetupBundle\Model\Form
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class Misc extends AbstractForm
{
    /**
     * @Form(message="Frontend language (de, en)", type="ask")
     */
    public $language;
}
