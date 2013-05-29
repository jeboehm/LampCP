<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle;

use Jeboehm\Lampcp\CoreBundle\DependencyInjection\ConfigBuilderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class JeboehmLampcpCoreBundle
 *
 * @package Jeboehm\Lampcp\CoreBundle
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class JeboehmLampcpCoreBundle extends Bundle
{
    /**
     * Add compiler pass.
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ConfigBuilderCompilerPass());
    }
}
