<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntityRepository;

abstract class AbstractConfigFixture {
	/**
	 * Tries to create a config entity
	 *
	 * @param \Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntity $entity
	 * @param \Doctrine\Common\Persistence\ObjectManager       $manager
	 *
	 * @return bool
	 */
	protected function _configEntity(ConfigEntity $entity, ObjectManager $manager) {
		$stored = $this
			->_getConfigEntityRepository($manager)
			->findOneByNameAndGroup($entity->getName(), $entity->getConfiggroup()->getName());

		if(!$stored) {
			$manager->persist($entity);

			return true;
		}

		return false;
	}

	/**
	 * Ensures, that a group exists
	 *
	 * @param string                                     $name
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 *
	 * @return \Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup
	 */
	protected function _configGroup($name, ObjectManager $manager) {
		/** @var $group ConfigGroup */
		$group = $this->_getConfigGroupRepository($manager)->findOneBy(array(
																			'name' => $name,
																	   ));

		if(!$group) {
			$group = new ConfigGroup();
			$group->setName($name);
			$manager->persist($group);
		}

		return $group;
	}

	/**
	 * Get ConfigGroup Repository
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 *
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	private function _getConfigGroupRepository(ObjectManager $manager) {
		return $manager->getRepository('JeboehmLampcpConfigBundle:ConfigGroup');
	}

	/**
	 * Get ConfigGroup Repository
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 *
	 * @return ConfigEntityRepository
	 */
	private function _getConfigEntityRepository(ObjectManager $manager) {
		return $manager->getRepository('JeboehmLampcpConfigBundle:ConfigEntity');
	}

}
