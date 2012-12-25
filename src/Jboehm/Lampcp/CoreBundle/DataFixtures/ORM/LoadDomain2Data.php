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
use Jboehm\Lampcp\CoreBundle\Entity\SystemUser;
use Jboehm\Lampcp\CoreBundle\Entity\Protection;
use Jboehm\Lampcp\CoreBundle\Entity\MailAccount;
use Jboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jboehm\Lampcp\CoreBundle\Entity\PathOption;
use Jboehm\Lampcp\CoreBundle\Entity\MailAddress;

class LoadDomain2Data implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$domain = new Domain();
		$domain
			->setDomain('uh.cx')
			->setPath('/var/www/uh.cx')
			->setGid(10001)
			->setHasMail(false)
			->setHasSSH(true)
			->setHasWeb(true);

		$manager->persist($domain);

		$systemuser = new SystemUser($domain);
		$systemuser
			->setName('user101')
			->setPassword('test123')
			->setUid(10001);

		$manager->persist($systemuser);

		$mysqldatabase = new MysqlDatabase($domain);
		$mysqldatabase
			->setName('uhcxsql1')
			->setPassword('test123')
			->setComment('Testdatenbank');

		$manager->persist($mysqldatabase);

		$manager->flush();
	}
}
