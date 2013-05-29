<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Command;

/**
 * Interface ConfigBuilderCommandInterface
 *
 * @package Jeboehm\Lampcp\CoreBundle\Command
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
interface ConfigBuilderCommandInterface
{
    /**
     * A list of entities that require an execution
     * of this command when they are changed.
     *
     * @return array
     */
    public static function getListenEntities();

    /**
     * Get the command's name.
     *
     * @return string
     */
    public static function getCommandName();
}
