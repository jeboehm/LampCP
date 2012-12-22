<?php

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
	 * @Route("/", name="status")
	 * @Template()
	 * @return array
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$domains = $em->getRepository('JboehmLampcpCoreBundle:Domain')->findAll();

		return array(
			'domainlist'     => $domains,
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Saves domain in the current session
	 *
	 * @Method("POST")
	 * @Route("/setdomain", name="set_domain")
	 * @throws NotFoundHttpException
	 * @return array
	 */
	public function setDomainAction() {
		$value  = $this->getRequest()->get('domainselector');
		$domain = null;

		if(!empty($value) && is_numeric($value)) {
			/** @var $domain Domain */
			$domain = $this
				->getDoctrine()
				->getManager()
				->getRepository('JboehmLampcpCoreBundle:Domain')
				->findOneById(intval($value));
		}

		if(!$domain) {
			throw $this->createNotFoundException('Domain not found');
		}

		$session = $this->_getSession();
		$session->set('domain', $domain->getId());

		return $this->forward('JboehmLampcpCoreBundle:Default:index');
	}
}
