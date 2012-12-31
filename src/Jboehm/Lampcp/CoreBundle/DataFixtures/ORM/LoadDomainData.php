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
use Jboehm\Lampcp\CoreBundle\Entity\MailAddress;
use Jboehm\Lampcp\CoreBundle\Entity\User;
use Jboehm\Lampcp\CoreBundle\Entity\Log;

class LoadDomainData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$user = new User();
		$user
			->setName('test1')
			->setUid(1000)
			->setGid(1000);

		$manager->persist($user);

		$domain = new Domain();
		$domain
			->setDomain('john-doe.invalid')
			->setUser($user)
			->setPath('/srv/www/john-doe.invalid')
			->setWebroot('htdocs');

		$manager->persist($domain);

		$subdomain = new Subdomain($domain);
		$subdomain
			->setPath('htdocs/cv')
			->setSubdomain('cv');

		$manager->persist($subdomain);

		$protection = new Protection($domain);
		$protection
			->setPath('htdocs/cv')
			->setRealm('Protected Area')
			->setUsername('john')
			->setPassword('johndoe');

		$manager->persist($protection);

		$mailaccount = new MailAccount($domain);
		$mailaccount
			->setUsername('johndoemail')
			->setPassword('test123')
			->setEnabled(true)
			->setQuota(32 * 1024)
			->setHasImap4(true)
			->setHasPop3(true)
			->setHasSmtp(true);

		$manager->persist($mailaccount);

		$mailaddress = new MailAddress($domain, $mailaccount);
		$mailaddress
			->setAddress('john')
			->setHasCatchAll(false)
			->setMailaccount($mailaccount);

		$manager->persist($mailaddress);

		$mysqldatabase = new MysqlDatabase($domain);
		$mysqldatabase
			->setName('johnsql1')
			->setPassword('test123')
			->setComment('Testdatabase');

		$manager->persist($mysqldatabase);

		$log = new Log();
		$log
			->setType($log::TYPE_INFO)
			->setMessage('Loaded John Doe fixtures');
		$manager->persist($log);

		$manager->flush();
	}
}
