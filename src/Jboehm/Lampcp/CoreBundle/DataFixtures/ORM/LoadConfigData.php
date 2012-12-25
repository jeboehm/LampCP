<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Uhcx\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jboehm\Lampcp\CoreBundle\Entity\Config;

class LoadConfigData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$config = array(
			'system/directorys/htdocsroot' => 'htdocs',
			'system/directorys/configroot' => 'etc',
			'system/mysql/rootuser'        => 'root',
			'system/mysql/rootpassword'    => '',
		);

		foreach($config as $path => $value) {
			$this->_createOption($path, $value, $manager);
		}

		$manager->flush();
	}

	/**
	 * Create Option
	 *
	 * @param string                                     $path
	 * @param string                                     $value
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	protected function _createOption($path, $value, ObjectManager $manager) {
		$config = new Config();
		$config
			->setPath($path)
			->setValue($value);

		$manager->persist($config);
	}
}
