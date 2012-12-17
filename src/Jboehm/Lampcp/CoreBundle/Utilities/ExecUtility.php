<?php

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
		$optionsClean = array();
		$returnString = '';
		$returnCode   = 0;
		$returnLines  = array();

		foreach($options as $name => $value) {
			$optionsClean[$name] = escapeshellarg($value);
		}

		$command      = $cmd . ' ' . join(' ', $optionsClean);
		$returnString = exec($command, $returnLines, $returnCode);

		// Close streams
		//self::_closeOutputStreams();

		return array('code'   => $returnCode,
					 'output' => $returnString);
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
