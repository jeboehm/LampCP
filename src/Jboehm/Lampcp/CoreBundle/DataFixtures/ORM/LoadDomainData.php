<?php

namespace Jboehm\Uhcx\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jboehm\Lampcp\CoreBundle\Entity\Domain;
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jboehm\Lampcp\CoreBundle\Entity\Systemuser;
use Jboehm\Lampcp\CoreBundle\Entity\Protection;
use Jboehm\Lampcp\CoreBundle\Entity\Mailaccount;

class LoadDomainData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$domain = new Domain();
		$domain
			->setDomain('ressourcenkonflikt.de')
			->setPath('/var/www/ressourcenkonflikt.de')
			->setGid(10000)
			->setHasMail(true)
			->setHasSSH(true)
			->setHasWeb(true);

		$manager->persist($domain);

		$subdomain = new Subdomain($domain);
		$subdomain
			->setPath('test')
			->setSubdomain('test');

		$manager->persist($subdomain);

		$systemuser = new Systemuser($domain);
		$systemuser
			->setName('user100')
			->setPassword('test123')
			->setUid(10000);

		$manager->persist($systemuser);

		$protection = new Protection($domain);
		$protection
			->setPath('test/geheim')
			->setRealm('Protected Area')
			->setUsername('jeff')
			->setPassword('test123');

		$manager->persist($protection);

		$mailaccount = new Mailaccount($domain);
		$mailaccount
			->setUsername('rnmail1')
			->setPassword('test123')
			->setEnabled(true)
			->setQuota(32 * 1024)
			->setUid(10001)
			->setHasImap4(true)
			->setHasPop3(true)
			->setHasSmtp(true);

		$manager->persist($mailaccount);

		$manager->flush();
	}
}
