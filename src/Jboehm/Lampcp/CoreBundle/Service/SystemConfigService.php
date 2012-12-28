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
use Doctrine\ORM\EntityManager;
use Jboehm\Lampcp\CoreBundle\Entity\Config;

class SystemConfigService {
	/** @var string */
	protected $_configFile;

	/** @var Parser */
	protected $_yaml;

	/** @var EntityManager */
	protected $_em;

	/** @var array */
	protected $_parsedTemplate;

	/**
	 * Konstruktor
	 *
	 * @param EntityManager $em
	 * @param string        $configFile
	 *
	 * @throws \Exception
	 */
	public function __construct($em, $configFile) {
		$this->_yaml       = new Parser();
		$this->_em         = $em;
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
		$newConfig = array();
		$i         = 0;

		try {
			$config = $this->_yaml->parse(file_get_contents($this->_configFile));
		} catch(\Symfony\Component\Yaml\Exception\ParseException $e) {
			throw new \Exception('Unable to parse YAML ' . $this->_configFile);
		}

		foreach($config['config'] as $group => $parameters) {
			$newConfig[$i] = array('groupname' => 'systemconfig.group.' . str_replace('_', '.', $group));
			$opt           = array();
			$x             = 0;

			foreach($parameters as $parameter => $options) {
				$optionName      = 'systemconfig.option.' . $group . '.' . str_replace('_', '.', $parameter);
				$opt[$x]         = array('optionname'  => $optionName,
										 'optionvalue' => $this->getParameter($optionName),
				);
				$opt[$x]['type'] = 'text';

				if(is_array($options)) {
//					if(in_array('password', $options)) {
//						$opt[$x]['type'] = 'password';
//					}

					if(in_array('bool', $options)) {
						$opt[$x]['type'] = 'checkbox';
					}
				}

				$x++;
			}

			$newConfig[$i]['options'] = $opt;
			$i++;
		}

		$this->_getConfigRepository();

		return ($this->_parsedTemplate = $newConfig);
	}

	/**
	 * @return array
	 */
	public function getConfigTemplate() {
		if(is_array($this->_parsedTemplate)) {
			return $this->_parsedTemplate;
		} else {
			return $this->_parseYaml();
		}
	}

	/**
	 * Get repository
	 *
	 * @return \Doctrine\ORM\EntityRepository
	 */
	protected function _getConfigRepository() {
		return $this->_em->getRepository('JboehmLampcpCoreBundle:Config');
	}

	/**
	 * Get Config Parameter
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getParameter($name) {
		/** @var $config Config */
		$name   = str_replace('_', '.', $name);
		$config = $this->_getConfigRepository()->findOneBy(array('path' => $name));

		if($config) {
			return $config->getValue();
		}

		return '';
	}

	/**
	 * Set Config Parameter
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setParameter($name, $value) {
		/** @var $config Config */
		$name   = str_replace('_', '.', $name);
		$config = $this->_getConfigRepository()->findOneBy(array('path' => $name));

		if($config) {
			$config->setValue($value);
			$config->setChanged(new \DateTime('now'));
		} else {
			$config = new Config();
			$config->setPath($name);
			$config->setValue($value);
		}

		$this->_em->persist($config);
		$this->_em->flush();
	}
}
