<?php

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Entity\Admin;
use Jboehm\Lampcp\CoreBundle\Form\AdminType;

/**
 * Default controller.
 */
class DefaultController extends Controller {
	/**
	 * Shows status page.
	 *
	 * @Route("/", name="status")
	 * @Template()
	 * @return array
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$domains = $em->getRepository('JboehmLampcpCoreBundle:Domain')->findAll();

		return array(
			'domainlist' => $domains,
		);
	}

	/**
	 * Saves domain in the current session
	 *
	 * @Method("POST")
	 * @Route("/setdomain", name="set_domain")
	 * @return array
	 */
	public function setDomainAction() {
		$value = $this->getRequest()->get('domainselector');

		if(!empty($value)) {
			// TODO Domain holen und in Session speichern
		}

		return $this->forward('JboehmLampcpCoreBundle:Default:index');
	}
}
