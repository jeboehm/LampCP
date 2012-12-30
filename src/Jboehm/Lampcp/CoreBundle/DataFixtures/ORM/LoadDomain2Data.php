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
use Jboehm\Lampcp\CoreBundle\Entity\Domain;
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jboehm\Lampcp\CoreBundle\Entity\Protection;
use Jboehm\Lampcp\CoreBundle\Entity\MailAccount;
use Jboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jboehm\Lampcp\CoreBundle\Entity\MailAddress;
use Jboehm\Lampcp\CoreBundle\Entity\User;

class LoadDomain2Data implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$user = new User();
		$user
			->setName('test2')
			->setUid(1002)
			->setGid(1005);

		$manager->persist($user);

		$domain = new Domain();
		$domain
			->setDomain('uh.cx')
			->setUser($user)
			->setPath('/var/www/uh.cx');

		$manager->persist($domain);

		$mysqldatabase = new MysqlDatabase($domain);
		$mysqldatabase
			->setName('uhcxsql1')
			->setPassword('test123')
			->setComment('Testdatenbank');

		$manager->persist($mysqldatabase);

		$manager->flush();
	}
}
