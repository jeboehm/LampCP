<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\UpdateBundle;

use Jeboehm\Lampcp\UpdateBundle\DependencyInjection\UpdateProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class JeboehmLampcpUpdateBundle
 *
 * Adds a Compiler Pass.
 *
 * @package Jeboehm\Lampcp\UpdateBundle
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class JeboehmLampcpUpdateBundle extends Bundle {
    /**
     * Add compiler pass
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container) {
        parent::build($container);

        $container->addCompilerPass(new UpdateProviderCompilerPass());
    }
}
