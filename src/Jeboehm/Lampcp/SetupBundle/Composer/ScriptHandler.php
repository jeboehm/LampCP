<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\SetupBundle\Composer;

use Composer\Script\CommandEvent;

/**
 * Class ScriptHandler
 *
 * @package Jeboehm\Lampcp\SetupBundle\Composer
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ScriptHandler
{
    /**
     * Show thank you message.
     *
     * @param CommandEvent $event
     */
    public static function showThankYouMessage(CommandEvent $event)
    {
        $messages = array(
            'Thank you for installing LampCP.',
            'For further information go to http://wiki.lampcp.de/',
        );

        echo join(PHP_EOL, $messages);
        echo PHP_EOL . PHP_EOL;
    }
}
