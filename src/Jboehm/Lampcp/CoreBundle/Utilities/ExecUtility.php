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

class ExecUtility {
	/**
	 * Execute command with options and return the output.
	 * This escapes arguments automatically.
	 *
	 * @param string $cmd
	 * @param array  $options
	 *
	 * @return array
	 */
	static public function exec($cmd, $options = array()) {
		$command     = $cmd;
		$returnCode  = 0;
		$returnLines = array();

		foreach($options as $name => $value) {
			if(!empty($name)) {
				$command .= ' ' . $name;
			}

			$command .= ' ' . escapeshellarg($value);
		}

		$returnString = exec($command, $returnLines, $returnCode);

		return array('code'   => $returnCode,
					 'output' => $returnString,
					 'lines'  => $returnLines);
	}

	/**
	 * Closes output streams to avoid hungs.
	 */
	static protected function _closeOutputStreams() {
		if(is_resource(STDOUT)) {
			fclose(STDOUT);
		}
		if(is_resource(STDERR)) {
			fclose(STDERR);
		}
	}
}
