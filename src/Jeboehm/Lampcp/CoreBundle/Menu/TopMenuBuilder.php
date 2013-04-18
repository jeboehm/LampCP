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

use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Service\DomainselectorService;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class TopMenuBuilder
 *
 * Builds the top menu, with the domainselector
 *
 * @package Jeboehm\Lampcp\CoreBundle\Menu
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
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

        // Domainselector aufbauen
        foreach ($this
                     ->_getDomainselectorService()
                     ->getDomains() as $domain) {
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
     * Get user menu
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function getUserMenu(FactoryInterface $factory, array $options) {
        $menu     = $factory->createItem('root');
        $loggedIn = $menu->addChild($this->_getUsername(), array(
                                                                'attributes' => array(
                                                                    'loggedasbutton' => true,
                                                                ),
                                                           ));

        $loggedIn->addChild('title.page.changepassword', array(
                                                              'route' => 'fos_user_change_password',
                                                         ));

        $loggedIn->addChild('title.page.logout', array(
                                                      'route' => 'fos_user_security_logout'
                                                 ));

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
        if ($this
            ->_getDomainselectorService()
            ->getSelected() === null
        ) {
            $text = 'nav.topmenu.nodomain';
        } else {
            $text = $this
                ->_getDomainselectorService()
                ->getSelected()
                ->getDomain();
        }

        return $text;
    }

    /**
     * Get username
     *
     * @return string
     */
    private function _getUsername() {
        /** @var $security SecurityContext */
        $security = $this->container->get('security.context');
        $user     = $security
            ->getToken()
            ->getUsername();

        return $user;
    }
}
