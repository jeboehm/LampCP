<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\UserLoaderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jboehm\Lampcp\ConfigBundle\Entity\ConfigGroup;
use Jboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;

class LoadConfigData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$group = new ConfigGroup();
		$group->setName('unix');
		$manager->persist($group);

		$entity = new ConfigEntity();
		$entity
			->setName('passwdfile')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/passwd');
		$manager->persist($entity);

		$entity = new ConfigEntity();
		$entity
			->setName('groupfile')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/group');
		$manager->persist($entity);

		$manager->flush();
	}
}
