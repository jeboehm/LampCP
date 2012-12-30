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
			->setDomain('ressourcenkonflikt.de')
			->setUser($user)
			->setPath('/var/www/ressourcenkonflikt.de');

		$manager->persist($domain);

		$subdomain = new Subdomain($domain);
		$subdomain
			->setPath('test')
			->setSubdomain('test');

		$manager->persist($subdomain);

		$protection = new Protection($domain);
		$protection
			->setPath('test/geheim')
			->setRealm('Protected Area')
			->setUsername('jeff')
			->setPassword('test123');

		$manager->persist($protection);

		$mailaccount = new MailAccount($domain);
		$mailaccount
			->setUsername('rnmail1')
			->setPassword('test123')
			->setEnabled(true)
			->setQuota(32 * 1024)
			->setHasImap4(true)
			->setHasPop3(true)
			->setHasSmtp(true);

		$manager->persist($mailaccount);

		$mailaddress = new MailAddress($domain, $mailaccount);
		$mailaddress
			->setAddress('jeff')
			->setHasCatchAll(false)
			->setMailaccount($mailaccount);

		$manager->persist($mailaddress);

		$mysqldatabase = new MysqlDatabase($domain);
		$mysqldatabase
			->setName('rnsql1')
			->setPassword('test123')
			->setComment('Testdatenbank');

		$manager->persist($mysqldatabase);

		$pathoption = new PathOption($domain);
		$pathoption
			->setPath('test/indexed')
			->setHasDirectoryListing(true);

		$manager->persist($pathoption);

		$manager->flush();
	}
}
