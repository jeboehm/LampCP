<?php

namespace Jboehm\Uhcx\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jboehm\Lampcp\CoreBundle\Entity\Admin;

class LoadAdminData implements FixtureInterface, ContainerAwareInterface {
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * {@inheritDoc}
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		$admin = new Admin();
		$admin->setEmail('jeff@ressourcenkonflikt.de');
		$admin->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));
		$this->_setPassword($admin, 'test123');

		$manager->persist($admin);
		$manager->flush();
	}

	protected function _setPassword(Admin $user, $password) {
		$factory  = $this->container->get('security.encoder_factory');
		$encoder  = $factory->getEncoder($user);
		$password = $encoder->encodePassword($password, $user->getSalt());
		$user->setPassword($password);
	}
}
