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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigGroup;
use Jeboehm\Lampcp\ConfigBundle\Entity\ConfigEntity;

class LoadConfigData extends AbstractConfigFixture implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$group = $this->_configGroup('mail', $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('accountprefix')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('lampcpmail');
		$this->_configEntity($entity, $manager);

		$manager->flush();
	}
}
