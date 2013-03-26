<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\AuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class JeboehmLampcpAuthBundle
 *
 * Parent: FOSUserBundle
 *
 * @package Jeboehm\Lampcp\AuthBundle
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class JeboehmLampcpAuthBundle extends Bundle {
	public function getParent() {
		return 'FOSUserBundle';
	}
}
