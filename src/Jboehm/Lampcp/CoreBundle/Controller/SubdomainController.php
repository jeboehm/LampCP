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
use Jboehm\Lampcp\CoreBundle\Entity\Subdomain;
use Jboehm\Lampcp\CoreBundle\Form\SubdomainType;

/**
 * Subdomain controller.
 *
 * @Route("/config/subdomain")
 */
class SubdomainController extends BaseController {
	/**
	 * Lists all Subdomain entities.
	 *
	 * @Route("/", name="config_subdomain")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('JboehmLampcpCoreBundle:Subdomain')->findByDomain($this->_getSelectedDomain());

		return array(
			'entities'       => $entities,
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Finds and displays a Subdomain entity.
	 *
	 * @Route("/{id}/show", name="config_subdomain_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Subdomain */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Subdomain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Subdomain entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return array(
			'entity'         => $entity,
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Displays a form to create a new Subdomain entity.
	 *
	 * @Route("/new", name="config_subdomain_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Subdomain($this->_getSelectedDomain());
		$form   = $this->createForm(new SubdomainType(), $entity);

		return array(
			'entity'         => $entity,
			'form'           => $form->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Creates a new Subdomain entity.
	 *
	 * @Route("/create", name="config_subdomain_create")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:Subdomain:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Subdomain($this->_getSelectedDomain());
		$form   = $this->createForm(new SubdomainType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_subdomain_show', array('id' => $entity->getId())));
		}

		return array(
			'entity'         => $entity,
			'form'           => $form->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Displays a form to edit an existing Subdomain entity.
	 *
	 * @Route("/{id}/edit", name="config_subdomain_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Subdomain */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Subdomain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Subdomain entity.');
		}

		$editForm   = $this->createForm(new SubdomainType(), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return array(
			'entity'         => $entity,
			'edit_form'      => $editForm->createView(),
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Edits an existing Subdomain entity.
	 *
	 * @Route("/{id}/update", name="config_subdomain_update")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:Subdomain:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Subdomain */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Subdomain')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Subdomain entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new SubdomainType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_subdomain_edit', array('id' => $id)));
		}

		return array(
			'entity'         => $entity,
			'edit_form'      => $editForm->createView(),
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Deletes a Subdomain entity.
	 *
	 * @Route("/{id}/delete", name="config_subdomain_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			/** @var $entity Subdomain */
			$entity = $em->getRepository('JboehmLampcpCoreBundle:Subdomain')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find Subdomain entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_subdomain'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm();
	}
}
