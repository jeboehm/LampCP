<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Service;

use Symfony\Component\Yaml\Parser;
use AppKernel;

class SystemConfigService {
	/** @var string */
	protected $_configFile;

	/** @var Parser */
	protected $_yaml;

	/**
	 * Konstruktor
	 *
	 * @param string $configFile
	 *
	 * @throws \Exception
	 */
	public function __construct($configFile) {
		$this->_yaml       = new Parser();
		$this->_configFile = realpath(__DIR__ . '/../' . $configFile);

		if(!is_readable($this->_configFile)) {
			throw new \Exception('Could not read file ' . $this->_configFile);
		}
	}

	/**
	 * Parse YAML
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function _parseYaml() {
		$config = array();

		try {
			$config = $this->_yaml->parse(file_get_contents($this->_configFile));
		} catch(\Symfony\Component\Yaml\Exception\ParseException $e) {
			throw new \Exception('Unable to parse YAML ' . $this->_configFile);
		}

		return $config;
	}

	/**
	 * @return array
	 */
	public function get() {
		return $this->_parseYaml();
	}
}
