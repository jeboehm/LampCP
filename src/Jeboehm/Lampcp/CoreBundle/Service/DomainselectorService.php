<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;

class DomainselectorService {
	/** @var \Doctrine\ORM\EntityManager */
	private $_em;

	/** @var \Symfony\Component\HttpFoundation\Session\Session */
	private $_session;

	/**
	 * Konstruktor
	 *
	 * @param \Doctrine\ORM\EntityManager                       $em
	 * @param \Symfony\Component\HttpFoundation\Session\Session $session
	 */
	public function __construct(EntityManager $em, Session $session) {
		$this->_em      = $em;
		$this->_session = $session;
	}

	/**
	 * Get domains
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getDomains() {
		/** @var $domains Collection */
		$domains = $this->_em
			->getRepository('JeboehmLampcpCoreBundle:Domain')
			->findBy(array(), array('domain' => 'asc'));

		return $domains;
	}

	/**
	 * Get selected domain
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain|null
	 */
	public function getSelected() {
		$id      = $this->_session->get('domain');
		$domains = $this->getDomains();

		if(is_numeric($id) && $id > 0) {
			foreach($domains as $domain) {
				/** @var $domain Domain */
				if($domain->getId() === $id) {
					return $domain;
				}
			}
		}

		return null;
	}
}
