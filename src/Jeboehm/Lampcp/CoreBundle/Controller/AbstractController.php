<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jeboehm\Lampcp\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bridge\Monolog\Logger;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\ConfigBundle\Service\ConfigService;
use Jeboehm\Lampcp\CoreBundle\Service\CryptService;
use Jeboehm\Lampcp\CoreBundle\Twig\DomainselectorExtension;

/**
 * Abstract controller.
 */
abstract class AbstractController extends Controller {
	/** @var Logger */
	private $_logger;

	/**
	 * Get system config service
	 *
	 * @return ConfigService
	 */
	protected function _getConfigService() {
		return $this->get('config');
	}

	/**
	 * Get session
	 *
	 * @return Session
	 */
	protected function _getSession() {
		return $this->get('session');
	}

	/**
	 * Get logger
	 *
	 * @return Logger
	 */
	protected function _getLogger() {
		if(!$this->_logger) {
			$this->_logger = $this->get('logger');
		}

		return $this->_logger;
	}

	/**
	 * Get selected domain
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain|null
	 */
	protected function _getSelectedDomain() {
		/** @var $domainselector DomainselectorExtension */
		$domainselector = $this->get('jeboehm_lampcp_core.domainselector');

		return $domainselector->getSelected();
	}

	/**
	 * Get CryptService
	 *
	 * @return CryptService
	 */
	protected function _getCryptService() {
		return $this->get('jeboehm_lampcp_core.cryptservice');
	}
}
