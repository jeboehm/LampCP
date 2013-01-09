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
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use Jboehm\Lampcp\ConfigBundle\Service\ConfigService;

/**
 * AbstractCommand class
 */
abstract class AbstractCommand extends ContainerAwareCommand {
	/** @var EntityManager */
	private $_em;

	/** @var ConfigService */
	private $_configService;

	/** @var Logger */
	private $_logger;

	/**
	 * Get doctrine
	 *
	 * @return EntityManager
	 */
	protected function _getDoctrine() {
		if(!$this->_em) {
			$this->_em = $this->getContainer()->get('doctrine.orm.entity_manager');
		}

		return $this->_em;
	}

	/**
	 * Get system config service
	 *
	 * @return \Jboehm\Lampcp\ConfigBundle\Service\ConfigService
	 */
	protected function _getConfigService() {
		if(!$this->_configService) {
			$this->_configService = $this->getContainer()->get('config');
		}

		return $this->_configService;
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
