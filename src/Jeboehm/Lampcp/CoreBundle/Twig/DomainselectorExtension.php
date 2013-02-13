<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Twig;

use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;

class DomainselectorExtension extends \Twig_Extension {
	/** @var \Doctrine\ORM\EntityManager */
	private $_em;

	/** @var \Symfony\Component\HttpFoundation\Session\Session */
	private $_session;

	public function __construct(EntityManager $em, Session $session) {
		$this->_em      = $em;
		$this->_session = $session;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return 'domainselector_extension';
	}

	/**
	 * Get function list
	 *
	 * @return array
	 */
	public function getFunctions() {
		return array(
			'domainselector_selected' => new \Twig_Function_Method($this, 'getSelected'),
			'domainselector_domains'  => new \Twig_Function_Method($this, 'getDomains'),
		);
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
