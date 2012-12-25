<?php
/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Jboehm\Lampcp\CoreBundle\Entity\Domain;

/**
 * Base controller.
 */
abstract class BaseController extends Controller {
	/**
	 * Get session
	 *
	 * @return Session
	 */
	protected function _getSession() {
		return $this->get('session');
	}

	/**
	 * Get the selected domain
	 *
	 * @return \Jboehm\Lampcp\CoreBundle\Entity\Domain|bool
	 */
	protected function _getSelectedDomain() {
		/** @var $domain Domain */
		$domain   = null;
		$domainId = $this->_getSession()->get('domain');

		if(is_numeric($domainId) && $domainId > 0) {
			$repo   = $this->getDoctrine()->getRepository('JboehmLampcpCoreBundle:Domain');
			$domain = $repo->findOneById($domainId);

			if($domain) {
				return $domain;
			} else {
				$session = $this->_getSession();
				$session->set('domain', 0);
			}
		}

		return null;
	}
}
