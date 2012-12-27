<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Utilities;

class PasswordGeneratorUtility {
	/**
	 * Generate password
	 *
	 * @param int  $len
	 * @param bool $specChars
	 *
	 * @return string
	 */
	static public function generate($len, $specChars = false) {
		$chars = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'));

		if($specChars) {
			$chars = array_merge($chars, array('#', '&', '@', '$', '_', '%', '?', '+'));
		}

		mt_srand((double)microtime() * 1000000);

		for($i = 1; $i <= (count($chars) * 2); $i++) {
			$swap         = mt_rand(0, count($chars) - 1);
			$tmp          = $chars[$swap];
			$chars[$swap] = $chars[0];
			$chars[0]     = $tmp;
		}

		return substr(implode('', $chars), 0, $len);
	}
}
