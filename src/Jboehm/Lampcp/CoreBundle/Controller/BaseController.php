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

			if(!$domain) {
				$session = $this->_getSession();
				$session->set('domain', 0);
			}
		}

		return $domain;
	}

	/**
	 * Erzeugt das Domainselector Formular
	 *
	 * @return \Symfony\Component\Form\Form
	 */
	protected function _createDomainselectorForm() {
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

	/**
	 * Get domains
	 *
	 * @return \Jboehm\Lampcp\CoreBundle\Entity\Domain[]
	 */
	private function _getDomains() {
		/** @var $domains Domain[] */
		$em      = $this->getDoctrine()->getManager();
		$domains = $em->getRepository('JboehmLampcpCoreBundle:Domain')->findAll();

		return $domains;
	}

	/**
	 * Return Funktion, die für alle Controller verwendet werden sollte
	 *
	 * @param array $arrReturn
	 *
	 * @return array
	 */
	protected function _getGlobalReturn(array $arrReturn) {
		$arrGlob = array(
			'domainselector_form' => $this->_createDomainselectorForm()->createView(),
			'selecteddomain'      => $this->_getSelectedDomain(),
		);

		return array_merge($arrGlob, $arrReturn);
	}
}
