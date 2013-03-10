<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\Service;

use Doctrine\ORM\EntityManager;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;

abstract class AbstractConfigProvider {
	/** @var \Doctrine\ORM\EntityManager */
	private $_em;

	/** @var \Jeboehm\Lampcp\ConfigBundle\Service\ConfigService */
	private $_config;

	/** @var ConfigGroup */
	private $_lastGroup;

	/**
	 * Konstruktor
	 *
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param ConfigService               $config
	 */
	public function __construct(EntityManager $em, ConfigService $config) {
		$this->_em     = $em;
		$this->_config = $config;
	}

	/**
	 * Get config service
	 *
	 * @return ConfigService
	 */
	protected function _getConfig() {
		return $this->_config;
	}

	/**
	 * Get doctrine
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function _getDoctrine() {
		return $this->_em;
	}

	/**
	 * Initialize config entities
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 * Create group
	 *
	 * @param string $name
	 *
	 * @return \Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup
	 */
	protected function _createGroup($name) {
		/** @var $group ConfigGroup */
		$name  = strtolower($name);
		$group = $this
			->_getDoctrine()
			->getRepository('JeboehmLampcpConfigBundle:ConfigGroup')
			->findOneBy(array('name' => $name));

		if(!$group) {
			$group = new ConfigGroup();
			$group->setName($name);

			$this->_getDoctrine()->persist($group);
			$this->_getDoctrine()->flush();
		}

		$this->_lastGroup = $group;

		return $group;
	}

	/**
	 * Create entity
	 *
	 * @param string                                          $name
	 * @param integer                                         $type
	 * @param \Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup $group
	 * @param string                                          $default
	 *
	 * @throws \Exception
	 * @return \Jeboehm\Lampcp\ConfigBundle\Service\AbstractConfigProvider
	 */
	protected function _createEntity($name, $type, ConfigGroup $group = null, $default = '') {
		if(!$group && !$this->_lastGroup) {
			throw new \Exception('No group provided!');
		}

		$name   = strtolower($name);
		$entity = $this
			->_getDoctrine()
			->getRepository('JeboehmLampcpConfigBundle:ConfigEntity')
			->findOneBy(array(
							 'name'        => $name,
							 'configgroup' => $group,
						));

		if(!$entity) {
			$entity = new ConfigEntity();
			$entity
				->setName($name)
				->setConfiggroup($group)
				->setType($type)
				->setValue($default);

			$this->_getDoctrine()->persist($entity);
			$this->_getDoctrine()->flush();
		}

		return $this;
	}
}