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
use Jeboehm\Lampcp\CoreBundle\DataFixtures\ORM\AbstractConfigFixture;

class LoadConfigData extends AbstractConfigFixture implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$group = $this->_configGroup('mysql', $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('rootuser')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('root');
		$this->_configEntity($entity, $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('rootpassword')
			->setConfiggroup($group)
			->setType($entity::TYPE_PASSWORD)
			->setValue('');
		$this->_configEntity($entity, $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('dbprefix')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('lampcpsql');
		$this->_configEntity($entity, $manager);

		$manager->flush();
	}
}
