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

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Jeboehm\Lampcp\CoreBundle\Entity\Domain;
use Jeboehm\Lampcp\CoreBundle\Entity\Admin;
use Jeboehm\Lampcp\CoreBundle\Utilities\ExecUtility;

/**
 * Status controller.
 */
class StatusController extends AbstractController {
	protected function _updateLastLogin() {
		/** @var $security SecurityContext */
		/** @var $user Admin */
		$security = $this->get('security.context');
		$user     = $security->getToken()->getUser();
		$user->setLastseen(new \DateTime());
		$this->getDoctrine()->getManager()->flush();
	}

	/**
	 * Shows status page.
	 *
	 * @Route("/", name="status")
	 * @Route("/", name="default")
	 * @Template()
	 * @return array
	 */
	public function indexAction() {
		$this->_updateLastLogin();

		$uptime = new ExecUtility();
		$uptime->exec('uptime');

		$uname = new ExecUtility();
		$uname->exec('uname -a');

		return array(
			'uptime' => $uptime->getOutput(),
			'uname'  => $uname->getOutput(),
		);
	}

	/**
	 * Saves the domain to session
	 *
	 * @Route("/setdomain/{id}", name="set_domain")
	 *
	 * @param int $id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function setDomainAction($id) {
		if(is_numeric($id)) {
			/** @var $domain Domain */
			$domain  = $this
				->getDoctrine()
				->getRepository('JeboehmLampcpCoreBundle:Domain')
				->findOneBy(array('id' => $id));
			$session = $this->_getSession();

			if(!$domain) {
				$session->set('domain', 0);
			} else {
				$session->set('domain', $domain->getId());
			}
		}

		return $this->redirect($this->generateUrl('status'));
	}
}
