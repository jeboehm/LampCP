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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Jboehm\Lampcp\CoreBundle\Entity\Domain;

/**
 * Default controller.
 */
class DefaultController extends BaseController {
	/**
	 * Shows status page.
	 *
	 * @Route("/", name="default")
	 * @Template()
	 * @return array
	 */
	public function indexAction() {
		return $this->_getGlobalReturn(array());
	}

	/**
	 * Saves the domain to session
	 *
	 * @Method("POST")
	 * @Route("/setdomain", name="set_domain")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function setDomainAction(Request $request) {
		$form = $this->_createDomainselectorForm();
		$form->bind($request);

		if($form->isValid()) {
			$data   = $form->getData();
			$domain = null;

			/** @var $domain Domain */
			$domain = $this
				->getDoctrine()
				->getManager()
				->getRepository('JboehmLampcpCoreBundle:Domain')
				->findOneById(intval($data['domain']));

			$session = $this->_getSession();

			if(!$domain) {
				$session->set('domain', 0);
			} else {
				$session->set('domain', $domain->getId());
			}
		}

		return $this->forward('JboehmLampcpCoreBundle:Default:index');
	}
}
