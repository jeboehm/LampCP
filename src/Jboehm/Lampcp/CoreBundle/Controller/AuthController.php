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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Auth controller.
 */
class AuthController extends BaseController {
	/**
	 * Shows login page.
	 *
	 * @Route("/login", name="auth_login")
	 * @Template()
	 * @return array
	 */
	public function loginAction() {
		$request = $this->getRequest();
		$session = $this->_getSession();

		// get the login error if there is one
		if($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		}

		return array(
			'last_username' => $session->get(SecurityContext::LAST_USERNAME),
			'error'         => $error
		);
	}

	/**
	 * Check login request
	 *
	 * @Route("/login_check", name="auth_check")
	 * @Template()
	 * @return array
	 */
	public function loginCheckAction() {
		return array();
	}

	/**
	 * Logout request
	 *
	 * @Route("/logout", name="auth_logout")
	 * @return array
	 */
	public function logoutAction() {
		return array();
	}
}
