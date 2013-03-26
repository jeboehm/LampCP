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

/**
 * Class GeneralMenuBuilder
 *
 * Builds the main menu
 *
 * @package Jeboehm\Lampcp\CoreBundle\Menu
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class GeneralMenuBuilder extends ContainerAware {
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

        $menu->addChild('nav.general.title', array(
                                                  'attributes' => array(
                                                      'class' => 'nav-header',
                                                  )
                                             ));

        $menu->addChild('title.page.systemconfig', array(
                                                        'route' => 'config_system',
                                                   ));

        $menu->addChild('title.page.ipaddress', array(
                                                     'route' => 'config_ipaddress',
                                                ));

        $menu->addChild('title.page.certificate', array(
                                                       'route' => 'config_certificate',
                                                  ));

        $menu->addChild('title.page.domain', array(
                                                  'route' => 'config_domain',
                                             ));

        return $menu;
    }
}
