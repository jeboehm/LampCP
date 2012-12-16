<?php

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;

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
		$domain = $this->_getSession()->get('domain');

		if($domain) {
			return $domain;
		}

		return null;
	}
}
