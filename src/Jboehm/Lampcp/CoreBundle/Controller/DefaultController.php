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
	 * Get domains
	 *
	 * @return \Jboehm\Lampcp\CoreBundle\Entity\Domain[]
	 */
	protected function _getDomains() {
		/** @var $domains Domain[] */
		$em      = $this->getDoctrine()->getManager();
		$domains = $em->getRepository('JboehmLampcpCoreBundle:Domain')->findAll();

		return $domains;
	}

	/**
	 * Shows status page.
	 *
	 * @Route("/", name="default")
	 * @Template()
	 * @return array
	 */
	public function indexAction() {
		return array(
			'selecteddomain' => $this->_getSelectedDomain(),
			'domainselector_form' => $this->_createDomainselectorForm()->createView(),
		);
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

			if(!$domain) {
				throw $this->createNotFoundException('Domain not found');
			}

			$session = $this->_getSession();
			$session->set('domain', $domain->getId());
		}

		return $this->forward('JboehmLampcpCoreBundle:Default:index');
	}

	/**
	 * Erzeugt das Domainselector Formular
	 *
	 * @return \Symfony\Component\Form\Form
	 */
	private function _createDomainselectorForm() {
		$selectedDomain   = $this->_getSelectedDomain();
		$domainSelectList = array();
		$selectedId       = 0;

		if($selectedDomain) {
			$selectedId = $selectedDomain->getId();
		}

		foreach($this->_getDomains() as $domain) {
			$domainSelectList[$domain->getId()] = $domain->getDomain();
		}

		return $this->createFormBuilder(array('domain' => $selectedId))
			->add('domain', 'choice', array(
									  'choices' => $domainSelectList,
									  ))
			->getForm();
	}
}
