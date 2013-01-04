<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\Common\Persistence\ObjectManager;
use Jboehm\Lampcp\CoreBundle\Service\SystemConfigService;
use Symfony\Bridge\Monolog\Logger;

/**
 * AbstractCommand class
 */
abstract class AbstractCommand extends ContainerAwareCommand {
	/** @var ObjectManager */
	private $_objectManager;

	/** @var SystemConfigService */
	private $_systemConfigService;

	/** @var Monolog */
	private $_logger;

	/**
	 * Get doctrine
	 *
	 * @return \Doctrine\Common\Persistence\ObjectManager
	 */
	protected function _getDoctrine() {
		if(!$this->_objectManager) {
			$this->_objectManager = $this->getContainer()->get('doctrine.orm.entity_manager');
		}

		return $this->_objectManager;
	}

	/**
	 * Get system config service
	 *
	 * @return \Jboehm\Lampcp\CoreBundle\Service\SystemConfigService
	 */
	protected function _getSystemConfigService() {
		if(!$this->_systemConfigService) {
			$this->_systemConfigService = $this->getContainer()->get('jboehm_lampcp_core.systemconfigservice');
		}

		return $this->_systemConfigService;
	}

	/**
	 * Get logger
	 *
	 * @return Logger
	 */
	protected function _getLogger() {
		if(!$this->_logger) {
			$this->_logger = $this->getContainer()->get('logger');
		}

		return $this->_logger;
	}
}
