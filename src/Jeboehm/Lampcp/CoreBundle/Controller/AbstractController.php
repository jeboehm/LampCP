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
	 * Get the selected domain
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain|bool
	 */
	protected function _getSelectedDomain() {
		/** @var $domain Domain */
		$domain   = null;
		$domainId = $this->_getSession()->get('domain');

		if(is_numeric($domainId) && $domainId > 0) {
			$repo   = $this->getDoctrine()->getRepository('JeboehmLampcpCoreBundle:Domain');
			$domain = $repo->findOneById($domainId);

			if(!$domain) {
				$session = $this->_getSession();
				$session->set('domain', 0);
			}
		}

		return $domain;
	}

	/**
	 * Return Funktion, die für alle Controller verwendet werden sollte
	 *
	 * @param array $arrReturn
	 *
	 * @return array
	 */
	protected function _getReturn(array $arrReturn) {
		$arrGlob = array(
			'domainselector_domains' => $this->_getDomainsForDomainselector(),
			'selecteddomain'         => $this->_getSelectedDomain(),
		);

		return array_merge($arrGlob, $arrReturn);
	}

	/**
	 * Get CryptService
	 *
	 * @return CryptService
	 */
	protected function _getCryptService() {
		return $this->get('jeboehm_lampcp_core.cryptservice');
	}

	/**
	 * Get all domains
	 *
	 * @return \Jeboehm\Lampcp\CoreBundle\Entity\Domain[]
	 */
	private function _getDomainsForDomainselector() {
		/** @var $domains Domain[] */
		$domains = $this
			->getDoctrine()
			->getRepository('JeboehmLampcpCoreBundle:Domain')
			->findBy(array(), array('domain' => 'asc'));

		return $domains;
	}
}
