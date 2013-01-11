<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Utilities;

class ExecUtility {
	protected $_retOutput;
	protected $_retOutputLined;
	protected $_retCode;
	protected $_run = false;

	/**
	 * Execute command with options.
	 * This escapes arguments automatically.
	 *
	 * @param string $cmd
	 * @param array  $options
	 *
	 * @return void
	 */
	public function exec($cmd, $options = array()) {
		$this->_run  = true;
		$command     = $cmd;
		$returnCode  = 0;
		$returnLines = array();

		foreach($options as $name => $value) {
			if(!empty($name)) {
				$command .= ' ' . $name;
			}

			$command .= ' ' . escapeshellarg($value);
		}

		$this->_retOutput      = exec($command, $returnLines, $returnCode);
		$this->_retCode        = $returnCode;
		$this->_retOutputLined = $returnLines;
	}

	/**
	 * Closes output streams to avoid hungs.
	 */
	protected function _closeOutputStreams() {
		if(is_resource(STDOUT)) {
			fclose(STDOUT);
		}
		if(is_resource(STDERR)) {
			fclose(STDERR);
		}
	}

	/**
	 * Get returncode
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function getCode() {
		if(!$this->_run) {
			throw new \Exception('Use Exec first!');
		}

		return $this->_retCode;
	}

	/**
	 * Get output
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getOutput() {
		if(!$this->_run) {
			throw new \Exception('Use Exec first!');
		}

		return $this->_retOutput;
	}
}
