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

use Jeboehm\Lampcp\CoreBundle\Service\DomainselectorService;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class DomainMenuBuilder
 *
 * Builds the domain menu, only shown when the domainselector
 * is filled.
 *
 * @package Jeboehm\Lampcp\CoreBundle\Menu
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class DomainMenuBuilder extends ContainerAware {
    /**
     * Build the top menu
     *
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array                      $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');

        if ($this
            ->_getDomainselectorService()
            ->getSelected() !== null
        ) {
            $menu->addChild($this
                ->_getDomainselectorService()
                ->getSelected()
                ->getDomain(), array(
                                    'attributes' => array(
                                        'class' => 'nav-header',
                                    )
                               ));

            $menu->addChild('title.page.dns', array(
                                                   'route' => 'config_dns',
                                              ));

            $menu->addChild('title.page.subdomain', array(
                                                         'route' => 'config_subdomain',
                                                    ));

            $menu->addChild('title.page.mailaddress', array(
                                                           'route' => 'config_mailaddress',
                                                      ));

            $menu->addChild('title.page.mysqldatabase', array(
                                                             'route' => 'config_mysqldatabase',
                                                        ));

            $menu->addChild('title.page.pathoption', array(
                                                          'route' => 'config_pathoption',
                                                     ));

            $menu->addChild('title.page.protection', array(
                                                          'route' => 'config_protection',
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
}
