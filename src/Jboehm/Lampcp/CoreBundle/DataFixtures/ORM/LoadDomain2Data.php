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
use Jboehm\Lampcp\CoreBundle\Entity\User;
use Jboehm\Lampcp\CoreBundle\Entity\Log;

class LoadDomain2Data implements FixtureInterface {
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$user = new User();
		$user
			->setName('test2')
			->setUid(1002)
			->setGid(1005)
			->setGroupname('test2');

		$manager->persist($user);

		$domain = new Domain();
		$domain
			->setDomain('jane-doe.invalid')
			->setUser($user)
			->setPath('/srv/www/jane-doe.invalid')
			->setWebroot('htdocs');

		$manager->persist($domain);

		$log = new Log();
		$log
			->setType($log::TYPE_INFO)
			->setMessage('Loaded Jane Doe fixtures');
		$manager->persist($log);

		$manager->flush();
	}
}
