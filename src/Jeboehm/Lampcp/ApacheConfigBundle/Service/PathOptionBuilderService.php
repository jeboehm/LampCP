<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\ApacheConfigBundle\Service;

use Jeboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderServiceInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\PathOption;

class PathOptionBuilderService extends AbstractBuilderService implements BuilderServiceInterface {
	const _twigPathOptionConf = 'JeboehmLampcpApacheConfigBundle:Apache2:pathoptions.conf.twig';
	const _pathOptionFileName = '30_directory_options.conf';

	/**
	 * Generate PathOption config
	 */
	protected function _generatePathOptionConf() {
		/** @var $pathoptions PathOption[] */
		$apacheConfigDir = $this->_getConfigService()->getParameter('apache.pathapache2conf');
		$configFilePath  = $apacheConfigDir . '/' . self::_pathOptionFileName;
		$pathoptions     = $this->_getDoctrine()->getRepository('JeboehmLampcpCoreBundle:PathOption')->findAll();
		$config          = $this->_renderTemplate(self::_twigPathOptionConf, array(
																				  'pathoptions' => $pathoptions,
																			 ));

		if(!is_writable(dirname($configFilePath))) {
			throw new CouldNotWriteFileException();
		}

		$this->_getLogger()->info('(ProtectionBuilderService) Generating PathOption Config:' . $configFilePath);
		file_put_contents($configFilePath, $config);
	}

	/**
	 * Build all configurations
	 */
	public function buildAll() {
		$this->_generatePathOptionConf();
	}
}
