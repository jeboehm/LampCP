<?php

namespace Jboehm\Uhcx\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jboehm\Lampcp\CoreBundle\Entity\Admin;

class LoadAdminData implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$admin = new Admin();
		$admin->setEmail('jeff@ressourcenkonflikt.de');
		$admin->setPassword('test123');

		$manager->persist($admin);
		$manager->flush();
	}
}
