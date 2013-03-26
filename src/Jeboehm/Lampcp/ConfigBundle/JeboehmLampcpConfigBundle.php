<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ConfigBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Jeboehm\Lampcp\ConfigBundle\DependencyInjection\ConfigProviderCompilerPass;

/**
 * Class JeboehmLampcpConfigBundle
 *
 * Adds a Compiler Pass
 *
 * @package Jeboehm\Lampcp\ConfigBundle
 * @author  Jeffrey Böhm <post@jeffrey-boehm.de>
 */
class JeboehmLampcpConfigBundle extends Bundle {
	/**
	 * Add compiler pass
	 *
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function build(ContainerBuilder $container) {
		parent::build($container);

		$container->addCompilerPass(new ConfigProviderCompilerPass());
	}
}
