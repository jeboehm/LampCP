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
use Jeboehm\Lampcp\CoreBundle\DataFixtures\ORM\AbstractConfigFixture;

class LoadConfigData extends AbstractConfigFixture implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$group = $this->_configGroup('apache', $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('pathwww')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/var/www');
		$this->_configEntity($entity, $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('pathapache2conf')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/apache2/sites-enabled');
		$this->_configEntity($entity, $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('pathphpini')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/php5/cgi/php.ini');
		$this->_configEntity($entity, $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('cmdapache2restart')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/init.d/apache2 restart');
		$this->_configEntity($entity, $manager);

		$entity = new ConfigEntity();
		$entity
			->setName('pathcertificate')
			->setConfiggroup($group)
			->setType($entity::TYPE_STRING)
			->setValue('/etc/ssl/lampcp');
		$this->_configEntity($entity, $manager);

		$manager->flush();
	}
}
