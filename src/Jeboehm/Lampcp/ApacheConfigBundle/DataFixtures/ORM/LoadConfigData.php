<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheBundle\DataFixtures\ORM;

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
		$group->setName('apache');
		$manager->persist($group);

		$entity = new ConfigEntity();
		$entity
			->setName('pathwww')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/var/www');
		$manager->persist($entity);

		$entity = new ConfigEntity();
		$entity
			->setName('pathapache2conf')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/apache2/sites-enabled');
		$manager->persist($entity);

		$entity = new ConfigEntity();
		$entity
			->setName('pathphpini')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/php5/cgi/php.ini');
		$manager->persist($entity);

		$entity = new ConfigEntity();
		$entity
			->setName('cmdapache2restart')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/init.d/apache2 restart');
		$manager->persist($entity);

		$manager->flush();
	}
}
