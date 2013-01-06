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
use Jboehm\Lampcp\CoreBundle\Entity\Log;

class LoadConfigData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$config = array('systemconfig.option.mysql.root.user'          => 'root',
						'systemconfig.option.mysql.root.password'      => 'changeme',
						'systemconfig.option.mysql.db.prefix'          => 'lampcpsql',
						'systemconfig.option.paths.web.root.dir'       => '/var/www',
						'systemconfig.option.paths.mail.root.dir'      => '/srv/mail',
						'systemconfig.option.paths.unix.passwd.file'   => '/etc/passwd',
						'systemconfig.option.paths.unix.group.file'    => '/etc/group',
						'systemconfig.option.apache.config.directory'  => '/etc/apache2/sites-enabled',
						'systemconfig.option.apache.config.php.ini'    => '/etc/php5/cgi/php.ini',
						'systemconfig.option.apache.config.restartcmd' => '/etc/init.d/apache2 restart',
		);

		foreach($config as $path => $value) {
			$this->_createOption($path, $value, $manager);
		}

		$log = new Log();
		$log
			->setType($log::TYPE_INFO)
			->setMessage('Loaded config fixtures');
		$manager->persist($log);

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
