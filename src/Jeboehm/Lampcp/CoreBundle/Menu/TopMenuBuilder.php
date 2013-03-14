<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Jeboehm\Lampcp\CoreBundle\Service\DomainselectorService;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;

class TopMenuBuilder extends ContainerAware {
	/**
	 * Build the top menu
	 *
	 * @param \Knp\Menu\FactoryInterface $factory
	 * @param array                      $options
	 *
	 * @return \Knp\Menu\ItemInterface
	 */
	public function getMenu(FactoryInterface $factory, array $options) {
		$menu           = $factory->createItem('root');
		$domainselector = $menu->addChild($this->_getDomainselectorTitle());
		$menu->addChild('title.page.logout', array(
												  'route' => 'fos_user_security_logout'
											 ));

		foreach($this->_getDomainselectorService()->getDomains() as $domain) {
			/** @var $domain Domain */
			$domainselector->addChild($domain->getDomain(), array(
																 'route'           => 'set_domain',
																 'routeParameters' => array(
																	 'domain' => $domain->getId(),
																 ),
															));
		}

		return $menu;
	}

	/**
	 * Get domainselector service
	 *
	 * @return DomainselectorService
	 */
	protected function _getDomainselectorService() {
		$service = $this->container->get('jeboehm_lampcp_core.domainselector');

		return $service;
	}

	/**
	 * Get title for domainselector menu
	 *
	 * @return string
	 */
	private function _getDomainselectorTitle() {
		if($this->_getDomainselectorService()->getSelected() === null) {
			$text = 'default.topmenu.no.domain';
		} else {
			$text = $this->_getDomainselectorService()->getSelected()->getDomain();
		}

		return $text;
	}
}
