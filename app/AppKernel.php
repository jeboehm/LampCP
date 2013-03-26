<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {
	public function registerBundles() {
		$bundles = array(
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new Braincrafted\BootstrapBundle\BraincraftedBootstrapBundle(),
			new FOS\UserBundle\FOSUserBundle(),
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Jboehm\Bundle\PasswdBundle\JboehmPasswdBundle(),
			new Jeboehm\Lampcp\CoreBundle\JeboehmLampcpCoreBundle(),
			new Jeboehm\Lampcp\UserLoaderBundle\JeboehmLampcpUserLoaderBundle(),
			new Jeboehm\Lampcp\ApacheConfigBundle\JeboehmLampcpApacheConfigBundle(),
			new Jeboehm\Lampcp\MysqlBundle\JeboehmLampcpMysqlBundle(),
			new Jeboehm\Lampcp\ConfigBundle\JeboehmLampcpConfigBundle(),
			new Jeboehm\Lampcp\PostfixBundle\JeboehmLampcpPostfixBundle(),
			new Jeboehm\Lampcp\LightyConfigBundle\JeboehmLampcpLightyConfigBundle(),
            new Jeboehm\Lampcp\AuthBundle\JeboehmLampcpAuthBundle(),
            new Jeboehm\Lampcp\ZoneGeneratorBundle\JeboehmLampcpZoneGeneratorBundle(),
		);

		if(in_array($this->getEnvironment(), array('dev', 'test'))) {
			$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
			$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
			$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
		}

		return $bundles;
	}

	public function registerContainerConfiguration(LoaderInterface $loader) {
		$loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
	}
}
