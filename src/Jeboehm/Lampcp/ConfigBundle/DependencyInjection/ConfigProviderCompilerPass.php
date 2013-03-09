<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigProviderCompilerPass implements CompilerPassInterface {
	public function process(ContainerBuilder $container) {
		if(!$container->hasDefinition('jeboehm_lampcp_config_configprovidercollector')) {
			return;
		}

		$definition = $container->getDefinition('jeboehm_lampcp_config_configprovidercollector');
		$services   = $container->findTaggedServiceIds('config.provider');

		foreach($services as $id => $attrib) {
			$definition->addMethodCall('addProvider', array(new Reference($id)));
		}
	}
}
