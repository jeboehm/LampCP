<?php

namespace Jboehm\Uhcx\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jboehm\Lampcp\CoreBundle\Entity\Domain;
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jboehm\Lampcp\CoreBundle\Entity\Systemuser;

class LoadDomainData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$domain = new Domain();
		$domain->setDomain('ressourcenkonflikt.de');
		$domain->setGid(10000);
		$domain->setPath('/var/www/ressourcenkonflikt.de');
		$domain->setHasMail(true);
		$domain->setHasSSH(true);
		$domain->setHasWeb(true);
		$manager->persist($domain);

		$subdomain = new Subdomain();
		$subdomain->setDomain($domain);
		$subdomain->setPath('test');
		$subdomain->setSubdomain('test');
		$manager->persist($subdomain);

		$systemuser = new Systemuser();
		$systemuser->setName('user100');
		$systemuser->setDomain($domain);
		$systemuser->setPassword('test123');
		$systemuser->setUid(10000);
		$manager->persist($systemuser);

		$manager->flush();
	}
}
