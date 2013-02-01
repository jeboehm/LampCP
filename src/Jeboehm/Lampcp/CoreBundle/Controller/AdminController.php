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
use Jeboehm\Lampcp\CoreBundle\Entity\Admin;
use Jeboehm\Lampcp\CoreBundle\Form\Type\AdminType;

/**
 * Admin controller.
 *
 * @Route("/config/admin")
 */
class AdminController extends AbstractController implements ICrudController {
	/**
	 * Generates a password
	 *
	 * @param \Jeboehm\Lampcp\CoreBundle\Entity\Admin $user
	 * @param                                         $password
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

		/** @var $entities Admin[] */
		$entities = $em
			->getRepository('JeboehmLampcpCoreBundle:Admin')
			->findBy(array(), array('email' => 'asc'));

		return $this->_getReturn(array(
									  'entities' => $entities,
								 ));
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
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Admin')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Admin entity.');
		}

		return $this->_getReturn(array(
									  'entity' => $entity,
								 ));
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

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
	}

	/**
	 * Creates a new Admin entity.
	 *
	 * @Route("/create", name="config_admin_create")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Admin:new.html.twig")
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

		return $this->_getReturn(array(
									  'entity' => $entity,
									  'form'   => $form->createView(),
								 ));
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
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Admin')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Admin entity.');
		}

		$editForm = $this->createForm(new AdminType(true), $entity);

		return $this->_getReturn(array(
									  'entity'    => $entity,
									  'edit_form' => $editForm->createView(),
								 ));
	}

	/**
	 * Edits an existing Admin entity.
	 *
	 * @Route("/{id}/update", name="config_admin_update")
	 * @Method("POST")
	 * @Template("JeboehmLampcpCoreBundle:Admin:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		/** @var $entity Admin */
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Admin')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Admin entity.');
		}

		$oldPassword = $entity->getPassword();

		$editForm = $this->createForm(new AdminType(true), $entity);
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

		return $this->_getReturn(array(
									  'entity'    => $entity,
									  'edit_form' => $editForm->createView(),
								 ));
	}

	/**
	 * Deletes a Admin entity.
	 *
	 * @Route("/{id}/delete", name="config_admin_delete")
	 */
	public function deleteAction($id) {
		$em     = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('JeboehmLampcpCoreBundle:Admin')->find($id);

		if(!$entity) {
			throw $this->createNotFoundException('Unable to find Admin entity.');
		}

		$em->remove($entity);
		$em->flush();

		return $this->redirect($this->generateUrl('config_admin'));
	}
}
