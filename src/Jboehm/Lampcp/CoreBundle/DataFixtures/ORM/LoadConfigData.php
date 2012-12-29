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
		$config = array('systemconfig.option.mysql.root.user'        => 'root',
						'systemconfig.option.mysql.root.password'    => 'abc',
						'systemconfig.option.paths.web.root.dir'     => '/srv/www',
						'systemconfig.option.paths.mail.root.dir'    => '/srv/mail',
						'systemconfig.option.paths.unix.passwd.file' => '/etc/passwd',
						'systemconfig.option.unix.min.user.uid'      => '1000',
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
		$config->setPath($path)->setValue($value);

		$manager->persist($config);
	}
}
