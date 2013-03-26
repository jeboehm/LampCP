<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\IBuilder;

/**
 * Class BuilderServiceInterface
 *
 * Interface for configuration building services.
 *
 * @package Jeboehm\Lampcp\ApacheConfigBundle\IBuilder
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
interface BuilderServiceInterface {
    /**
     * @return void
     */
    public function buildAll();
}
