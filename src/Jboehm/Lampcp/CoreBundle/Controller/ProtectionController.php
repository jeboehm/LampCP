<?php

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Entity\Protection;
use Jboehm\Lampcp\CoreBundle\Form\ProtectionType;

/**
 * Protection controller.
 *
 * @Route("/config/protection")
 */
class ProtectionController extends BaseController {
	/**
	 * Lists all Protection entities.
	 *
	 * @Route("/", name="config_protection")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('JboehmLampcpCoreBundle:Protection')->findAll();

		return array(
			'entities'       => $entities,
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Finds and displays a Protection entity.
	 *
	 * @Route("/{id}/show", name="config_protection_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Protection */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Protection')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Protection entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return array(
			'entity'         => $entity,
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Displays a form to create a new Protection entity.
	 *
	 * @Route("/new", name="config_protection_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Protection($this->_getSelectedDomain());
		$form   = $this->createForm(new ProtectionType(), $entity);

		return array(
			'entity'         => $entity,
			'form'           => $form->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Creates a new Protection entity.
	 *
	 * @Route("/create", name="config_protection_create")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:Protection:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Protection($this->_getSelectedDomain());
		$form   = $this->createForm(new ProtectionType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_protection_show', array('id' => $entity->getId())));
		}

		return array(
			'entity'         => $entity,
			'form'           => $form->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Displays a form to edit an existing Protection entity.
	 *
	 * @Route("/{id}/edit", name="config_protection_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Protection */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Protection')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Protection entity.');
		}

		$editForm   = $this->createForm(new ProtectionType(), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return array(
			'entity'         => $entity,
			'edit_form'      => $editForm->createView(),
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Edits an existing Protection entity.
	 *
	 * @Route("/{id}/update", name="config_protection_update")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:Protection:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Protection */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Protection')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Protection entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new ProtectionType(), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_protection_edit', array('id' => $id)));
		}

		return array(
			'entity'         => $entity,
			'edit_form'      => $editForm->createView(),
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Deletes a Protection entity.
	 *
	 * @Route("/{id}/delete", name="config_protection_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			/** @var $entity Protection */
			$entity = $em->getRepository('JboehmLampcpCoreBundle:Protection')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find Protection entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_protection'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm();
	}
}
