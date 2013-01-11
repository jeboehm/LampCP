<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\ApacheConfigBundle\Service;

use Jboehm\Lampcp\ApacheConfigBundle\Exception\CouldNotWriteFileException;
use Jboehm\Lampcp\CoreBundle\Entity\PathOption;

class PathOptionBuilderService extends AbstractBuilderService {
	const _twigApachePathOptionConf = 'JboehmLampcpApacheConfigBundle:Default:pathoptions.conf.twig';

	/**
	 * Render Apache's PathOption config
	 *
	 * @param PathOption[] $pathoptions
	 *
	 * @return string
	 */
	protected function _renderApachePathOptionConfig(array $pathoptions) {
		return $this->_getTemplating()->render(self::_twigApachePathOptionConf, array(
																					 'pathoptions' => $pathoptions,
																				));
	}

	/**
	 * Generate Apache's PathOption config
	 */
	protected function _generateApachePathOptionConf() {
		/** @var $pathoptions PathOption[] */
		$apacheConfigDir = $this->_getConfigService()->getParameter('apache.pathapache2conf');
		$filename        = '99_pathoptions.conf';
		$configFilePath  = $apacheConfigDir . '/' . $filename;
		$pathoptions     = $this->_getDoctrine()->getRepository('JboehmLampcpCoreBundle:PathOption')->findAll();
		$config          = $this->_renderApachePathOptionConfig($pathoptions);

		$this->_getLogger()->info('(ProtectionBuilderService) Generating PathOption Config:' . $configFilePath);
		file_put_contents($configFilePath, $config);
	}

	/**
	 * Build all configurations
	 */
	public function buildAll() {
		$this->_generateApachePathOptionConf();
	}
}
