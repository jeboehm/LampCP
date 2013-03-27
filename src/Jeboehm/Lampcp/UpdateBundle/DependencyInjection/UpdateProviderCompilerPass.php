<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UpdateBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class UpdateProviderCompilerPass
 *
 * Collects "update.provider" tagged services
 *
 * @package Jeboehm\Lampcp\UpdateBundle\DependencyInjection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class UpdateProviderCompilerPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container) {
        if (!$container->hasDefinition('jeboehm_lampcp_update.updateexecutor')) {
            return;
        }

        $definition = $container->getDefinition('jeboehm_lampcp_update.updateexecutor');
        $services   = $container->findTaggedServiceIds('update.provider');

        foreach ($services as $id => $attrib) {
            $definition->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
