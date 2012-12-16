<?php

namespace Jboehm\Lampcp\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jboehm\Lampcp\CoreBundle\Entity\Admin;
use Jboehm\Lampcp\CoreBundle\Form\AdminType;

/**
 * Admin controller.
 *
 * @Route("/config/admin")
 */
class AdminController extends BaseController {
	/**
	 * Generates a password
	 *
	 * @param \Jboehm\Lampcp\CoreBundle\Entity\Admin $user
	 * @param                                        $password
	 *
	 * @return string
	 */
	protected function _getPassword(Admin $user, $password) {
		$factory  = $this->container->get('security.encoder_factory');
		$encoder  = $factory->getEncoder($user);
		$password = $encoder->encodePassword($password, $user->getSalt());

		return $password;
	}

	/**
	 * Lists all Admin entities.
	 *
	 * @Route("/", name="config_admin")
	 * @Template()
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('JboehmLampcpCoreBundle:Admin')->findAll();

		return array(
			'entities'       => $entities,
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Finds and displays a Admin entity.
	 *
	 * @Route("/{id}/show", name="config_admin_show")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Admin */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Admin')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Admin entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return array(
			'entity'      => $entity,
			'delete_form' => $deleteForm->createView(),
		);
	}

	/**
	 * Displays a form to create a new Admin entity.
	 *
	 * @Route("/new", name="config_admin_new")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Admin();
		$form   = $this->createForm(new AdminType(), $entity);

		return array(
			'entity'         => $entity,
			'form'           => $form->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Creates a new Admin entity.
	 *
	 * @Route("/create", name="config_admin_create")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:Admin:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Admin();
		$form   = $this->createForm(new AdminType(), $entity);
		$form->bind($request);

		if($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$entity->setPassword($this->_getPassword($entity, $entity->getPassword()));

			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_admin_show', array('id' => $entity->getId())));
		}

		return array(
			'entity'         => $entity,
			'form'           => $form->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Displays a form to edit an existing Admin entity.
	 *
	 * @Route("/{id}/edit", name="config_admin_edit")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Admin */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Admin')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Admin entity.');
		}

		$entity->setPassword('');

		$editForm   = $this->createForm(new AdminType(true), $entity);
		$deleteForm = $this->createDeleteForm($id);

		return array(
			'entity'         => $entity,
			'edit_form'      => $editForm->createView(),
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Edits an existing Admin entity.
	 *
	 * @Route("/{id}/update", name="config_admin_update")
	 * @Method("POST")
	 * @Template("JboehmLampcpCoreBundle:Admin:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Admin */
		$entity = $em->getRepository('JboehmLampcpCoreBundle:Admin')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Admin entity.');
		}

		$oldPassword = $entity->getPassword();

		$deleteForm = $this->createDeleteForm($id);
		$editForm   = $this->createForm(new AdminType(true), $entity);
		$editForm->bind($request);

		if($editForm->isValid()) {
			if(!$entity->getPassword()) {
				$entity->setPassword($oldPassword);
			} else {
				$entity->setPassword($this->_getPassword($entity, $entity->getPassword()));
			}

			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('config_admin_edit', array('id' => $id)));
		}

		return array(
			'entity'         => $entity,
			'edit_form'      => $editForm->createView(),
			'delete_form'    => $deleteForm->createView(),
			'selecteddomain' => $this->_getSelectedDomain(),
		);
	}

	/**
	 * Deletes a Admin entity.
	 *
	 * @Route("/{id}/delete", name="config_admin_delete")
	 * @Method("POST")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->bind($request);

		if($form->isValid()) {
			$em     = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('JboehmLampcpCoreBundle:Admin')->find($id);

			if(!$entity) {
				throw $this->createNotFoundException('Unable to find Admin entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('config_admin'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm();
	}
}
