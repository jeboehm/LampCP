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
use Jeboehm\Lampcp\ApacheConfigBundle\IBuilder\BuilderInterface;
use Jeboehm\Lampcp\CoreBundle\Entity\PathOption;

class PathOptionBuilderService extends AbstractBuilderService implements BuilderInterface {
	const _twigPathOptionConf = 'JeboehmLampcpApacheConfigBundle:Default:pathoptions.conf.twig';

	/**
	 * Render PathOption config
	 *
	 * @param PathOption[] $pathoptions
	 *
	 * @return string
	 */
	protected function _renderPathOptionConfig(array $pathoptions) {
		return $this->_getTemplating()->render(self::_twigPathOptionConf, array(
																					 'pathoptions' => $pathoptions,
																				));
	}

	/**
	 * Generate PathOption config
	 */
	protected function _generatePathOptionConf() {
		/** @var $pathoptions PathOption[] */
		$apacheConfigDir = $this->_getConfigService()->getParameter('apache.pathapache2conf');
		$filename        = '99_pathoptions.conf';
		$configFilePath  = $apacheConfigDir . '/' . $filename;
		$pathoptions     = $this->_getDoctrine()->getRepository('JeboehmLampcpCoreBundle:PathOption')->findAll();
		$config          = $this->_renderPathOptionConfig($pathoptions);

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
