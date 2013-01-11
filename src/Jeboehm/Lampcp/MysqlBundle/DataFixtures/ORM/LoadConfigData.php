<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\MysqlBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;

class LoadConfigData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$group = new ConfigGroup();
		$group->setName('mysql');
		$manager->persist($group);

		$entity = new ConfigEntity();
		$entity
			->setName('rootuser')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('root');
		$manager->persist($entity);

		$entity = new ConfigEntity();
		$entity
			->setName('rootpassword')
			->setConfiggroup($group)
			->setType($entity::TYPE_PASSWORD)
			->setValue('');
		$manager->persist($entity);

		$entity = new ConfigEntity();
		$entity
			->setName('dbprefix')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('lampcpsql');
		$manager->persist($entity);

		$manager->flush();
	}
}
