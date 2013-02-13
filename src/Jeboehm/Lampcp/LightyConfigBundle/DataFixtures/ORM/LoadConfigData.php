<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\LightyConfigBundle\DataFixtures\ORM;

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
		$group = $this->_configGroup('lighttpd', $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('pathlighttpdconf')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/lighttpd/conf-enabled');
		$this->_configEntity($entity, $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('cmdlighttpdrestart')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/init.d/lighttpd restart');
		$this->_configEntity($entity, $manager);

		$manager->flush();
	}
}
