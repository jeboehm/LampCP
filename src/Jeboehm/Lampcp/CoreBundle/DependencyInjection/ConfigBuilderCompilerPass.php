<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ConfigBuilderCompilerPass
 *
 * Collects the services tagged with 'config.builder'.
 *
 * @package Jeboehm\Lampcp\CoreBundle\DependencyInjection
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class ConfigBuilderCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jeboehm_lampcp_core.configbuildercommandcollector')) {
            return;
        }

        $definition = $container->getDefinition('jeboehm_lampcp_core.configbuildercommandcollector');
        $services   = $container->findTaggedServiceIds('config.builder');

        foreach ($services as $id => $attrib) {
            $definition->addMethodCall('addBuilder', array(new Reference($id)));
        }
    }
}
