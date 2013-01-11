<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Uhcx\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jeboehm\Lampcp\CoreBundle\Entity\Protection;
use Jeboehm\Lampcp\CoreBundle\Entity\MailAccount;
use Jeboehm\Lampcp\CoreBundle\Entity\MysqlDatabase;
use Jeboehm\Lampcp\CoreBundle\Entity\MailAddress;
use Jeboehm\Lampcp\CoreBundle\Entity\User;
use Jeboehm\Lampcp\CoreBundle\Entity\Log;

class LoadDomainData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$user = new User();
		$user
			->setName('test1')
			->setUid(1000)
			->setGid(1000)
			->setGroupname('test1');

		$manager->persist($user);

		$domain = new Domain();
		$domain
			->setDomain('john-doe.invalid')
			->setUser($user)
			->setPath('/var/www/john-doe.invalid')
			->setWebroot('htdocs');

		$manager->persist($domain);

		$subdomain = new Subdomain($domain);
		$subdomain
			->setPath('htdocs/cv')
			->setSubdomain('cv');

		$manager->persist($subdomain);

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
			->setName('lampcpsql1')
			->setPassword('test123')
			->setComment('Testdatabase');

		$manager->persist($mysqldatabase);

		$manager->flush();
	}
}
